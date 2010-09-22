<?php

$app = "frontend";
include(dirname(__FILE__).'/../../../../test/bootstrap/functional.php');
$browser = new sfTestBrowser();
$browser->initialize();
$con = Propel::getConnection();

$con->begin();
try
{
  // run the test
  $t = new lime_test(13, new lime_output_color());
  $t->diag('sfAssetFolderPeer');
  
  $sfAssetFolder = sfAssetFolderPeer::retrieveByPath(sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media'));
  $t->ok($sfAssetFolder->isRoot(), 'retrieveByPath() retrieves root from app_swDoctrineAssetsLibrary_upload_dir string');

  $sfAssetFolder = sfAssetFolderPeer::retrieveByPath();
  $t->ok($sfAssetFolder->isRoot(), 'retrieveByPath() retrieves root from empty string');
  
  $sfAssetFolder = sfAssetFolderPeer::createFromPath(sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').'/simple');
  $t->isa_ok($sfAssetFolder, 'sfAssetFolder', 'createFromPath() creates a sfAssetFolder from simple string');
  $t->isa_ok($sfAssetFolder->getParent(), 'sfAssetFolder', 'createFromPath() from simple string has a parent');
  $t->ok($sfAssetFolder->getParent()->isRoot(), 'createFromPath() creates a root child from simple string');

  $sfAssetFolder2 = sfAssetFolderPeer::createFromPath(sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').'/simple/subfolder');
  $t->isa_ok($sfAssetFolder2, 'sfAssetFolder', 'createFromPath() creates a sfAssetFolder from simple string');
  $t->is($sfAssetFolder2->getParent()->getId(), $sfAssetFolder->getId(), 'createFromPath() from simple string parent is correct');

  $sfAssetFolder = sfAssetFolderPeer::createFromPath(sfConfig::get('app_swDoctrineAssetsLibrary_upload_dir', 'media').'/second/subfolder');
  $t->ok($sfAssetFolder instanceof sfAssetFolder, 'createFromPath() creates a sfAssetFolder from simple string');
  $t->ok($sfAssetFolder->getParent() instanceof sfAssetFolder, 'createFromPath() from composed string has a parent');
  $t->ok($sfAssetFolder->getParent()->getParent()->isRoot(), 'createFromPath() creates a root child from composed string');
  
  $sfAssetFolder = sfAssetFolderPeer::createFromPath('third/subfolder');
  $t->ok($sfAssetFolder instanceof sfAssetFolder, 'createFromPath() creates a sfAssetFolder from simple string');
  $t->ok($sfAssetFolder->getParent() instanceof sfAssetFolder, 'createFromPath() from composed string has a parent');
  $t->ok($sfAssetFolder->getParent()->getParent()->isRoot(), 'createFromPath() creates a root child from composed string');
}
catch (Exception $e)
{
  // do nothing
}

// reset DB
$con->rollback();

