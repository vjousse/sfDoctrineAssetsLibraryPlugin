<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginsfAssetTable extends Doctrine_Table
{

  public static function presents($folderId, $filename)
  {
    $count = Doctrine::getTable('sfAsset')
      ->createQuery()
      ->where('folder_id = ? AND filename = ?', array($folderId, $filename))
      ->count();
    
    return $count > 0 ? true : false;
  }
  
  /**
   * Retrieves a sfAsset object from a relative URL like
   *    /medias/foo/bar.jpg
   * i.e. the kind of URL returned by $sf_asset->getUrl()
   */
  public static function retrieveFromUrl($url)
  {
    $url = sfAssetFolderPeer::cleanPath($url);
    list($relPath, $filename) = sfAssetsLibraryTools::splitPath($url);
    
     return Doctrine::getTable('sfAsset a')
      ->createQuery()
      ->leftJoin('sfAssetFolder f')
      ->where('f.relative_path = ? AND a.filename = ', $relPath ?  $relPath : null, $filename)
      ->fetchOne();
  }
}