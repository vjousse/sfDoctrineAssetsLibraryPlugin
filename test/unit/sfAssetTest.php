<?php

$_test_dir = realpath(dirname(__FILE__).'/..');

require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($configuration);

include($configuration->getSymfonyLibDir().'/vendor/lime/lime.php');

sfContext::createInstance($configuration);

Doctrine_Manager::getInstance();


$browser = new sfTestBrowser();
$browser->initialize();

$t = $browser->test();

$manager = new sfDatabaseManager($configuration);
$con = $manager->getDatabase('doctrine')->getConnection();

$doctrine_conn = Doctrine_Manager::getInstance()->getConnection('doctrine');
$doctrine_conn->beginTransaction();

try
{
  $t->diag('sfAsset');
  
  $sfAsset = new sfAsset();
  $sfAsset->setsfAssetFolder(sfAssetFolderTable::getRoot());
  $t->isa_ok($sfAsset->getsfAssetFolder(), 'sfAssetFolder', 'sfAsset can have root as folder');
  $sfAsset->setFilename('filename.jpg');
  $t->is($sfAsset->getRelativePath(), sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').  DIRECTORY_SEPARATOR . 'filename.jpg', 'getRelativePath() gives correct result');

  
  sfConfig::set('sf_web_dir', '/tmp');
  sfConfig::set('app_swDoctrineAssetsLibrary_upload_dir','media');
  $t->is($sfAsset->getFullPath(), sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media'). DIRECTORY_SEPARATOR .'filename.jpg','getFullPath() gives complete path'); 
  $t->is($sfAsset->getFullPath('large'), sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').DIRECTORY_SEPARATOR .'thumbnail/large_filename.jpg','getFullPath() gives correct thumbnail path'); 
  
  $t->is($sfAsset->getUrl(),DIRECTORY_SEPARATOR .sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').DIRECTORY_SEPARATOR .'filename.jpg','getUrl() gives correct url');
  $t->is($sfAsset->getUrl('small'),DIRECTORY_SEPARATOR . sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').DIRECTORY_SEPARATOR .'thumbnail/small_filename.jpg','getUrl() gives correct thumbnail url');

  $assets_path = dirname(__FILE__).'/../assets/';
  $test_asset = $assets_path . 'raikkonen.jpg';
  $t->ok(is_file($test_asset), 'test asset found');
  
  $sfAsset = new sfAsset();
  $sfAsset->setsfAssetFolder(sfAssetFolderTable::getRoot());
  $sfAsset->createAsset($test_asset, false);
  $t->is($sfAsset->getFilename(),'raikkonen.jpg', 'create() gives correct filename');
  $t->is((int)$sfAsset->getFilesize(), 18, 'create() gives correct size');
  $t->ok($sfAsset->isImage(), 'create() gives correct type');
  $t->ok(is_file($sfAsset->getFullPath()), 'create() physically copies asset');
  if($sfAsset->supportsThumbnails())
  {
    $t->ok(is_file($sfAsset->getFullPath('large')), 'create() physically creates thumbnail');
  }
  else
  {
    $t->diag('please activate thumbnails support');
  }
  
  $old_path = $sfAsset->getFullPath();
  $old_thumb_path = $sfAsset->getFullPath('large');
 
  $sfAsset->save($doctrine_conn);
  $sfAsset->setFilename('raikkonen2.jpg');
  $sfAsset->save($doctrine_conn);
  
  $t->is($sfAsset->getFilename(),'raikkonen2.jpg', 'move() changes filename');
  $t->ok(is_file($sfAsset->getFullPath()), 'move() physically moves asset');
  $t->ok(! is_file($old_path),'move() physically moves asset');
  
  if($sfAsset->supportsThumbnails())
  {
    $t->ok(is_file($sfAsset->getFullPath('large')), 'move() physically moves thumbnail');
    $t->ok(! is_file($old_thumb_path),'move() physically moves thumbnail');
  }
  else
  {
    $t->diag('please activate thumbnails support');
  }
  
  $old_path = $sfAsset->getFullPath();
  $old_thumb_path = $sfAsset->getFullPath('large');
  $old_id = $sfAsset->getId();
  $sfAsset->delete();
  
  $t->ok(! is_file($old_path),'delete() physically removes asset');
  if($sfAsset->supportsThumbnails())
  {
    $t->ok(! is_file($old_thumb_path),'delete() physically removes thumbnail');
  }
  else
  {
    $t->diag('please activate thumbnails support');
  }  

  $null = Doctrine::getTable('sfAsset')->find($old_id);
  $t->ok(! $null,'delete() removes asset from DB');

}
catch (Exception $e)
{
  $t->ok(false, 'Exception : '.$e->getMessage() );
}


// reset DB
$doctrine_conn->rollback();