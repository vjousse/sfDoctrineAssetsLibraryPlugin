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
  // run the test
  $t->diag('sfAssetFolder');
  
  $root = sfAssetFolderTable::getRoot();
  $t->isa_ok($root, 'sfAssetFolder', 'root folder exists');
  
  $sfAssetFolder = new sfAssetFolder();
  $sfAssetFolder->setName('Test_Directory');
  $sfAssetFolder->getNode()->insertAsFirstChildOf($root);
  $sfAssetFolder->save();
  
  $t->is($sfAssetFolder->getRelativePath(), $root->getRelativePath() . '/' . $sfAssetFolder->getName(),'getRelativePath() is updated on save');
  
  $sfAssetFolder2 = new sfAssetFolder();
  $sfAssetFolder2->setName('Test_Sub-directory');
  $sfAssetFolder2->getNode()->insertAsFirstChildOf($sfAssetFolder);
  $sfAssetFolder2->save();
  
  $t->is($sfAssetFolder2->getRelativePath(), $sfAssetFolder->getRelativePath() . '/' . $sfAssetFolder2->getName(),'getRelativePath() is updated on save for subfolders');
  
  $assets_path = dirname(__FILE__).'/../assets/';
  $test_asset = $assets_path . 'raikkonen.jpg';
  $t->ok(is_file($test_asset), 'test asset found');  
  $sfAsset = new sfAsset();
  
  $sfAsset->setsfAssetFolder($sfAssetFolder2);
  $sfAsset->createAsset($test_asset, false);
  $sfAsset->save();
  
  $t->ok(is_file($sfAsset->getFullPath()), 'asset found');
  $sf_asset_id = $sfAsset->getId();
  
  $sfAssetFolder3 = new sfAssetFolder();
  $sfAssetFolder3->setName('Test_Sub-sub-directory');
  $sfAssetFolder3->getNode()->insertAsFirstChildOf($sfAssetFolder2);
  $sfAssetFolder3->save();
  
  $t->is($sfAssetFolder3->getRelativePath(), $sfAssetFolder2->getRelativePath() . '/' . $sfAssetFolder3->getName(),'getRelativePath() is updated on save for subfolders');
  
  $t->diag($sfAssetFolder3->getRelativePath());
  
  $sfAsset2 = new sfAsset();
  $sfAsset2->setsfAssetFolder($sfAssetFolder3);
  $sfAsset2->createAsset($test_asset, false);
  $sfAsset2->save();
  $t->ok(is_file($sfAsset2->getFullPath()), 'asset2 found');

  $sf_asset2_id = $sfAsset2->getId();  
  $id3 = $sfAssetFolder3->getId();

  $sfAssetFolder2->move($root);

  $t->diag($sfAssetFolder3->getRelativePath());
  $sfAssetFolder3 = Doctrine::getTable('sfAssetFolder')->find($id3);

  $t->is($sfAssetFolder2->getNode()->getParent()->getId(),$root->getId(),'move() gives the correct parent');
  $t->is($sfAssetFolder3->getNode()->getParent()->getNode()->getParent()->getId(),$root->getId(),'move() changes descendants grandparents');
  $t->is($sfAssetFolder2->getRelativePath(), $root->getRelativePath() . '/' . $sfAssetFolder2->getName(),'move() changes descendants relative paths');
  $t->is($sfAssetFolder3->getRelativePath(), $sfAssetFolder2->getRelativePath() . '/' . $sfAssetFolder3->getName(),'move() changes descendants relative paths');
  
  
  $sfAsset =  Doctrine::getTable('sfAsset')->find($sf_asset_id);
  $sfAsset2 =  Doctrine::getTable('sfAsset')->find($sf_asset2_id);
  $t->ok(is_file($sfAsset->getFullPath()), 'base asset of moved folder found');
  $t->ok(is_file($sfAsset2->getFullPath()), 'deep asset of moved folder found');
}
catch (Exception $e)
{
  echo $e->getMessage();
}

// reset DB
$doctrine_conn->rollback();

