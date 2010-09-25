<?php

class sfPakeMakeRootTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
    new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
    ));

    $this->addOptions(array(
    new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
    ));

    $this->namespace = 'sfAsset';
    $this->name = 'create-root';
    $this->briefDescription = 'create a root node for the asset library';

  }

  protected function execute($arguments = array(), $options = array())
  {

    $databaseManager = new sfDatabaseManager($this->configuration);

    if (sfAssetFolderTable::getRoot())
    {
      throw new sfCommandException('The asset library already has a root');
    }

    $this->logSection('sfAsset', sprintf("Creating root node at %s...", sfConfig::get('app_sfDoctrineAssetsLibrary_upload_dir', 'media')));
    sfAssetFolderTable::createRoot();
    $this->logSection('sfAsset', "Root Node Created");

  }
}