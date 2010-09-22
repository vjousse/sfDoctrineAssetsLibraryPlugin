<?php

$app = "frontend";
include(dirname(__FILE__).'/../../../../test/bootstrap/functional.php');
$browser = new sfTestBrowser();
$browser->initialize();
$con = Propel::getConnection();

$con->begin();
try
{
  $t = new lime_test(5, new lime_output_color());
  $t->diag('sfAssetPeer');

  $con->begin();
  $t->is(sfAssetPeer::retrieveFromUrl(sfAssetFolderPeer::getRoot()->getRelativePath() . '/filename.jpg'), null, 'sfAssetPeer::retrieveFromUrl() returns null when a URL is not found');
  $t->is(sfAssetPeer::exists(sfAssetFolderPeer::getRoot()->getId(), 'filename.jpg'), false, 'sfAssetPeer::exists() returns false when an asset is not found');
  
  $sfAsset = new sfAsset();
  $sfAsset->setsfAssetFolder(sfAssetFolderPeer::getRoot());
  $sfAsset->setFilename('filename.jpg');
  $sfAsset->save($con);
  $t->is(sfAssetPeer::retrieveFromUrl(sfAssetFolderPeer::getRoot()->getRelativePath() . '/filename.jpg')->getId(), $sfAsset->getId(), 'sfAssetPeer::retrieveFromUrl() finds an asset from its URL');
  $t->is(sfAssetPeer::retrieveFromUrl($sfAsset->getUrl())->getId(), $sfAsset->getId(), 'sfAssetPeer::retrieveFromUrl() finds an asset from the result of `getUrl()`');
  $t->is(sfAssetPeer::exists(sfAssetFolderPeer::getRoot()->getId(), 'filename.jpg'), true, 'sfAssetPeer::exists() returns true when an asset is found');
  
}
catch (Exception $e)
{
  echo $e->getMessage();
}

// reset DB
$con->rollback();