<?php
/*
 * This file is part of the sfAssetsLibrary package.
 * 
 * (c) 2007 William Garcia <wgarcia@clever-age.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfAssetsLibraryToolkit toolkit class
 * 
 * @author William Garcia
 */
class sfAssetsLibraryTools
{
 
  /**
   * @return string
   */
  public static function dot2slash($txt)
  {
    return preg_replace('#[\+\s]+#', '/', $txt);
  }

  public static function getType($filepath)
  {
    $suffix = substr($filepath, strrpos($filepath, '.') - strlen($filepath) + 1);
    if (self::isImage($suffix))
    {
      return 'image';
    }
    else if (self::isText($suffix))
    {
      return 'txt';
    }
    else if (self::isArchive($suffix))
    {
      return 'archive';
    }
    else
    {
      return $suffix;
    }
  }
  
  public static function isImage($ext)
  {
    return in_array(strtolower($ext), array('png', 'jpg', 'jpeg', 'gif'));
  }
  
  public static function isText($ext)
  {
    return in_array(strtolower($ext), array('txt', 'php', 'markdown'));
  }

  public static function isArchive($ext)
  {
    return in_array(strtolower($ext), array('zip', 'gz', 'tgz', 'rar'));
  }
  
  public static function getInfo($dir, $filename)
  {
    $info = array();
    $info['ext']  = substr($filename, strpos($filename, '.') - strlen($filename) + 1);
    $stats = stat($dir.'/'.$filename);
    $info['size'] = $stats['size'];
    $info['thumbnail'] = true;
    if (self::isImage($info['ext']))
    {
      if (is_readable($dir.'/thumbnail/small_'.$filename))
      {
        $info['icon'] = $dir.'/thumbnail/small_'.$filename;
      }
      else
      {
        $info['icon'] = $dir.'/'.$filename;
        $info['thumbnail'] = false;
      }
    }
    else
    {
      $info['icon'] = '/swDoctrineAssetsLibraryPlugin/images/unknown.png';
      if (is_readable(sfConfig::get('sf_web_dir').'/sfAssetsLibraryPlugin/images/'.$info['ext'].'.png'))
      {
        $info['icon'] = '/swDoctrineAssetsLibraryPlugin/images/'.$info['ext'].'.png';
      }
    }

    return $info;
  }
  
  public static function sanitizeName($file)
  {
    return preg_replace('/[^a-z0-9_\.-]/i', '_', $file);
  }
  
  public static function mkdir($dirName, $parentDirName)
  {
    $dirName = rtrim($dirName, '/');
    
    if (!is_dir(self::getMediaDir(true) . $parentDirName))
    {
      list($parent, $name) = self::splitPath($parentDirName);
      if ($parent && $name)
      {
        $result = self::mkdir($name, $parent);
        if (!$result)
        {
          return false;
        }
      }
    }
    
    if (!$dirName)
    {
      throw new sfException('Trying to make a folder with no name');
    }
    $parentDirName = ($parentDirName)? rtrim($parentDirName, '/') . '/' : '';
    $absCurrentDir = self::getMediaDir(true).$parentDirName.$dirName;
    $absThumbDir   = $absCurrentDir.DIRECTORY_SEPARATOR.sfConfig::get('app_swDoctrineAssetsLibrary_thumbnail_dir', 'thumbnail');
    $mkdir_success = true;
    try
    {
      $old = umask(0);
      if (!is_dir($absCurrentDir))
      {
        mkdir($absCurrentDir, 0770);
      }
      if (!is_dir($absThumbDir))
      {
        mkdir($absThumbDir, 0770);
      }
      umask($old);
    }
    catch(sfException $e)
    {
      $mkdir_success = false;
    }

    return $mkdir_success;
  }

  public static function deleteTree($root) 
  {
    if (!is_dir($root)) 
    {
      return false;
    }
    foreach(glob($root.'/*', GLOB_ONLYDIR) as $dir) 
    {
      if (!is_link($dir)) 
      {
        self::deleteTree($dir);
      }
    }
    
    return rmdir($root);
  }

  public static function createAssetUrl($path, $filename, $thumbnail_type = 'full', $file_system = true)
  {
    if ($thumbnail_type == 'full')
    {
      return self::getMediaDir($file_system) . $path . DIRECTORY_SEPARATOR . $filename;    
    }
    else
    {
      return self::getMediaDir($file_system) . self::getThumbnailDir($path) . $thumbnail_type . '_' . $filename;
    }
  }  
  
  public function getAssetImageTag($sf_media, $thumbnail_type = 'full', $file_system = false, $options = array())
  {
    $options = array_merge($options, array(
      'alt'   => $sf_media->getCopyright(),
      'title' => $sf_media->getCopyright()
    ));
    
    return image_tag(self::getAssetUrl($sf_media, $thumbnail_type, $file_system), $options);
  }
  
  /**
   * Retrieves a sfMedia object from a relative URL like
   *    /medias/foo/bar.jpg
   * i.e. the kind of URL returned by getAssetUrl($sf_media, 'full', false)
   */
  public static function getAssetFromUrl($url)
  { 
    
    $url = str_replace(sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media'), '', $url);
    $url = rtrim($url, '/');
    $parts = explode('/', $url);
    $filename = array_pop($parts);
    $relPath = '/' . implode('/', $parts);
    
    $c = new Criteria();
    $c->add(sfMediaPeer::FILENAME, $filename);
    $c->add(sfMediaPeer::REL_PATH, $relPath ?  $relPath : null);
    
    return sfMediaPeer::doSelectOne($c);
  }
  
  public static function getMediaDir($file_system = false)
  {
    $upload_dir = rtrim(sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media'), '/').'/';
    if ($file_system)
    {
      return sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR;
    }
    else
    {
      return '/';
    }
  }

  /**
   * Gives thumbnails folder for a folder 
   *
   * @param string $path
   * @return string
   */
  public static function getThumbnailDir($path)
  {
    $thumb_dir = $path . '/' . sfConfig::get('app_swDoctrineAssetsLibrary_thumbnail_dir', 'thumbnail');
    
    return rtrim($thumb_dir, '/').'/';
  }
  
  public static function getThumbnailPath($path, $filename, $thumbnail_type = 'full')
  {
    if ($thumbnail_type == 'full')
    {
      return self::getMediaDir(true) . $path . '/' . $filename;
    }
    else
    {
      return self::getMediaDir(true) . self::getThumbnailDir($path) . $thumbnail_type . '_' . $filename;
    }
  }
  
  /**
   * Create the thumbnails for image assets
   * The numbe and size of thumbnails can be configured in the app.yml
   * The configuration accepts various formats:
   *   small: { width: 80, height: 80, shave: true }  // 80x80 shaved
   *   small: [80, 80, true]                          // 80x80 shaved
   *   small: [80]                                    // 80x80 not shaved
   */
  public static function createThumbnails($folder, $filename)
  {
    $source = self::getThumbnailPath($folder, $filename, 'full');
    $thumbnailSettings = sfConfig::get('app_swDoctrineAssetsLibrary_thumbnails', array(
      'small' => array('width' => 84, 'height' => 84, 'shave' => true),
      'large' => array('width' => 194, 'height' => 152)
    ));
    foreach ($thumbnailSettings as $key => $params)
    {
      $width  = $params['width'];
      $height = $params['height'];
      $shave  = isset($params['shave']) ? $params['shave'] : false;
      self::createThumbnail($source, self::getThumbnailPath($folder, $filename, $key), $width, $height, $shave);
    }
  }
  
  /**
   * Resize automatically an image 
   * Options : shave_all
   * Recommanded when  "image source HEIGHT" < "image source WIDTH"
   */
  public static function createThumbnail($source, $dest, $width, $height, $shave_all = false)
  {
    if (class_exists('sfThumbnail') and file_exists($source))
    {
      if(sfConfig::get('app_swDoctrineAssetsLibrary_use_ImageMagick', false))
      {
        $adapter = 'sfImageMagickAdapter';
        $mime = 'image/jpg';
      }
      else
      {
        $adapter = 'sfGDAdapter';
        $mime = 'image/jpeg';
      }
      if($shave_all)
      {
        $thumbnail  = new sfThumbnail($width, $height, false, true, 85, $adapter, array('method' => 'shave_all'));
        $thumbnail->loadFile($source);
        $thumbnail->save($dest, $mime);
        return true;
      }
      else
      {
        list($w, $h, $type, $attr) = getimagesize($source);
        $newHeight = ceil(($width * $h) / $w);
        $thumbnail = new sfThumbnail($width, $newHeight, true, true, 85, $adapter);
        $thumbnail->loadFile($source);
        $thumbnail->save($dest, $mime);
        return true;
      }
    }
    return false;
  }
  
  public static function getParent($path)
  {
    $dirs = explode('/', $path);
    array_pop($dirs);
    
    return join('/', $dirs);
  }

  /**
   * Splits a path into a basepath and a name
   *
   * @param string $path
   * @return array $relative_path $name
   */
  public static function splitPath($path, $separator = DIRECTORY_SEPARATOR)
  {
    $path = rtrim($path, $separator);
    $dirs = preg_split('/' . preg_quote($separator, '/') . '+/', $path);
    $name = array_pop($dirs);
    $relative_path =  implode($separator, $dirs);
    
    return array($relative_path, $name);
  }
  
  public static function log($message, $color = '')
  {
    switch($color)
    {
      case 'green':
        $message = "\033[32m".$message."\033[0m\n";
        break;
      case 'red':
        $message = "\033[31m".$message."\033[0m\n";
        break;
      case 'yellow':
        $message = "\033[33m".$message."\033[0m\n";
        break;
      default:
        $message = $message . "\n";
    }
    fwrite(STDOUT, $message);
  }
  
}