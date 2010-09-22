<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginsfAssetFolder extends BasesfAssetFolder
{

  protected $old_path = null; // allow to check if the directory has been renamed
  
  public function getFullPath()
  {
    return sfAssetsLibraryTools::getMediaDir(true) . $this->getRelativePath();
  }
  
  /**
   * Folder physically exists
   *
   * @return bool
   */
  public function presents()
  {
    return is_dir($this->getRelativePath()) && is_writable($this->getRelativePath());
  }

  /**
   * Checks if a name already exists in the list of subfolders to a folder
   *
   * @param string $name A folder name
   * @return bool
   */
  public function hasSubFolder($name)
  {
    if(!$this->getNode()->hasChildren())
    {
      return false;
    }
    
    foreach($this->getNode()->getChildren() as $child)
    {
      if($child->getName() == $name)
      {
        return true;
      }
    }
    
    return false;
    
  }

  public function hasSibling($name)
  {
    
    // bug : the getParent method reload the node with the value from the 
    //       database, so we loose modified data...
    $values = $this->toArray(false);
    $siblings = $this->getNode()->getSiblings();
    $this->fromArray($values, false);
    
    foreach($siblings as $sibling)
    {
      if($name === $sibling->getName())
      {
        return true;
      }
    }
    
    return false;
  }
  /**
   * Physically creates folder
   *
   * @return bool succes
   */
  public function createFolder()
  {
    list ($base, $name ) = sfAssetsLibraryTools::splitPath($this->getRelativePath());
    return sfAssetsLibraryTools::mkdir($name, $base);
  }

  public function hasAsset($asset_name)
  {
    
    return $this->getsfAssets()->count() > 0;
  }
  
  public function getAssetsWithFilenames()
  {
    // TODO : try to use the key map value from collection
    $filenames = array();
    foreach ($this->getsfAssets() as $asset)
    {
      $filenames[$asset->getFilename()] = $asset;
    }
    
    return $filenames;
  }

  public function getSubfoldersWithFolderNames()
  {
    
    // TODO : try to use the key map value from the collection
    $children = array();
    if($this->getNode()->hasChildren())
    {
      $children = $this->getNode()->getChildren();
    }
    
    $foldernames = array();
    foreach ($children as $child)
    {
      $foldernames[$child->getName()] = $child;
    }
    
    return $foldernames;
  }
  
  /**
   * Synchronize with a physical folder 
   *
   * @param string $base_folder base folder path
   * @param Boolean $verbose If true, every file or database operation will issue an alert in STDOUT
   * @param Boolean $removeOrphanAssets If true, database assets with no associated file are removed
   * @param Boolean $removeOrphanFolders If true, database folders with no associated directory are removed
   */
  public function synchronizeWith($base_folder, $verbose = true, $removeOrphanAssets = false, $removeOrphanFolders = false)
  {
    if (!is_dir($base_folder))
    {
      throw new sfAssetException(sprintf('%s is not a directory', $base_folder));
    }

    $files = sfFinder::type('file')->maxdepth(0)->ignore_version_control()->in($base_folder);

    $assets = $this->getAssetsWithFilenames();
    foreach($files as $file)
    {
      if (!array_key_exists(basename($file), $assets))
      {
        // File exists, asset does not exist: create asset
        $sfAsset = new sfAsset();
        $sfAsset->setFolderId($this->getId());
        $sfAsset->createAsset($file, false);
        try {
          $sfAsset->save();
        } catch(sfAssetException $e) {
          sfAssetsLibraryTools::log($e->getMessage(), 'red');
        }
        
        if($verbose)
        {
          sfAssetsLibraryTools::log(sprintf("Importing file %s", $file), 'green');
        }
      }
      else
      {
        // File exists, asset exists: do nothing
        unset($assets[basename($file)]);
      }
    }
    
    foreach ($assets as $name => $asset)
    {
      if($removeOrphanAssets)
      {
        // File does not exist, asset exists: delete asset
        $asset->delete();
        if($verbose)
        {
          sfAssetsLibraryTools::log(sprintf("Deleting asset %s", $asset->getUrl()), 'yellow');
        }
      }
      else
      {
        if($verbose)
        {
          sfAssetsLibraryTools::log(sprintf("Warning: No file for asset %s", $asset->getUrl()), 'red');
        }
      }
    }

    $dirs = sfFinder::type('dir')->maxdepth(0)->discard(sfConfig::get('app_swDoctrineAssetsLibrary_thumbnail_dir', 'thumbnail'))->ignore_version_control()->in($base_folder);
    $folders = $this->getSubfoldersWithFolderNames();

    foreach($dirs as $dir)
    {
      list(,$name) = sfAssetsLibraryTools::splitPath($dir);
      if (!array_key_exists($name, $folders))
      {
        // dir exists in filesystem, not in database: create folder in database
        $sfAssetFolder = new sfAssetFolder();
        $sfAssetFolder->setName($name);
        $sfAssetFolder->save();
        
        $sfAssetFolder->getNode()->insertAsLastChildOf($this);
        if($verbose)
        {
          sfAssetsLibraryTools::log(sprintf("Importing directory %s", $dir), 'green');
        }
      }
      else
      {
        // dir exists in filesystem and database: look inside
        $sfAssetFolder = $folders[$name];
        unset($folders[$name]);
      }
      $sfAssetFolder->synchronizeWith($dir, $verbose, $removeOrphanAssets, $removeOrphanFolders);
    }
    
    foreach ($folders as $name => $folder)
    {
      if($removeOrphanFolders)
      {
        $folder->delete(null, true);
        if($verbose)
        {
          sfAssetsLibraryTools::log(sprintf("Deleting folder %s", $folder->getRelativePath()), 'yellow');
        }
      }
      else
      {
        if($verbose)
        {
          sfAssetsLibraryTools::log(sprintf("Warning: No directory for folder %s", $folder->getRelativePath()), 'red');
        }
      }
    }
    
  }
  
  /**
   * Recursively move assets and folders from $old_path to $new_path
   *
   * @param string $old_path
   * @param string $new_path
   * @return bool success
   */
  static public function movePhysically($old_path, $new_path)
  {

    if(!is_dir($old_path))
    {
      return true;
    }
   
    if (!is_dir($new_path) || !is_writable($new_path))
    {
      $old = umask(0);
      mkdir($new_path, 0770);
      umask($old);      
    }
     
    $files = sfFinder::type('file')->maxdepth(0)->in($old_path);
    $success = true;

    foreach ($files as $file)
    {
      $success = rename($file, $new_path . '/' . basename($file)) && $success;
    }
    if ($success)
    {
      $folders = sfFinder::type('dir')->maxdepth(0)->in($old_path);
      foreach($folders as $folder)
      {
        $new_name = substr($folder, strlen($old_path));
        $success = self::movePhysically($folder, $new_path . '/' . $new_name) && $success;
      }
    }
    
    $success = rmdir($old_path) && $success;

    return $success;
  }
  
  /**
   * Move under a new parent
   *
   * @param sfAssetFolder $new_parent
   */
  public function move(sfAssetFolder $new_parent)
  {
    // controls
    if($this->getNode()->isRoot())
    {
      throw new sfAssetException('The root folder cannot be moved');
    }
    else if($new_parent->hasSubFolder($this->getName()))
    {
      throw new sfAssetException('The target folder "%folder%" already contains a folder named "%name%". The folder has not been moved.', array('%folder%' => $new_parent, '%name%' => $this->getName()));
    }
    else if($new_parent->getNode()->isDescendantOf($this))
    {
      throw new sfAssetException('The target folder cannot be a subfolder of moved folder. The folder has not been moved.');
    }
    else if ($this->getId() == $new_parent->getId())
    {
      return;
    }
    
    $old_path = $this->getFullPath();
    
    $this->getNode()->moveAsLastChildOf($new_parent);
    $this->save();
    
    $descendants = $this->getNode()->getChildren();
    $descendants = $descendants ? $descendants : array();
    
    // move its assets
    self::movePhysically($old_path, $this->getFullPath());
    
    foreach ($descendants as $descendant)
    {
      // Update relative path
      $descendant->save();
    }
    
    // else: nothing to do
  }

  /**
   * Change folder name
   *
   * @param string $name
   */
  private function rename()
  {

    if($this->hasSibling($this->getName()))
    {
      throw new sfAssetException('The parent folder already contains a folder named "%name%". The folder has not been renamed.', array('%name%' => $this->getName()));
    }
    
    if(sfAssetsLibraryTools::sanitizeName($this->getName()) != $this->getName())
    {
      throw new sfAssetException('The target folder name "%name%" contains incorrect characters. The folder has not be renamed.', array('%name%' => $this->getName()));
    }

    
    $new_path = $this->getFullPath();
    $old_path = $this->old_path;
    
    // move its assets
    self::movePhysically($old_path, $new_path);
    
    if($this->getNode()->hasChildren())
    {
      
      $children = $this->getNode()->getChildren();
      
      foreach ($children as $descendant)
      {
        $descendant->save();
      }
    }
    
  }
  
  /**
   * Also delete all contents
   *
   * @param Connection $con
   * @param Boolean $force If true, do not throw an exception if the physical directories cannot be removed
   */
  public function delete(Doctrine_Connection $con = null, $force = false)
  {
    static $inDelete;
    
    $this->getsfAssets()->delete();
    
    // delete node will call this method
    // so we have to avoid a recursive endless call.
    if(is_null($inDelete))
    {
      $inDelete = true;
      $this->getNode()->delete();
      sfToolkit::clearDirectory($this->getFullPath());
      $inDelete = null;
    }
   
    return parent::delete($con);
  }
  
  public function getParentPath()
  {
    if($this->getNode()->isRoot())
    {
      throw new sfException('Root node has no parent path');
    }
    
    $path = $this->getRelativePath();
    
    return trim(substr($path, 0, strrpos($path, '/')), '/');
  }

  public function setName($name)
  {

    if($name != $this->getName() && is_null($this->old_path) && $this->exists())
    {
      $this->old_path = $this->getFullPath();
    }
    
    $this->_set('name', $name);
  }
  
  public function preSave($event)
  {

    $modifiedFields = $this->getModified();

    if(!array_key_exists('relative_path', $modifiedFields))
    {
      if($this->getNode()->getParent())
      {
        $this->setRelativePath($this->getNode()->getParent()->getRelativePath().'/'.$this->getName());
      }
      else
      {
        $this->setRelativePath($this->getName());
      }
    }
    
    // physical existence
    if (!$this->presents())
    {
      if (!$this->createFolder())
      {      
        throw new sfAssetException('Impossible to create folder "%name%"', array('%name%' => $this->getRelativePath()));
      }
    }
  }

  public function save(Doctrine_Connection $conn = null)
  {
    $exists = $this->exists();

    parent::save($conn);

    if(!is_null($this->old_path) && $exists)
    {
      $this->rename();
      $this->old_path = null;
    }
  }
}