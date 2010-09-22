<?php

class sfPakeSynchroniseTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('dirname', sfCommandArgument::REQUIRED, 'The directory to synchronise'),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_REQUIRED, 'verbose', true),
      new sfCommandOption('removeOrphanAssets', null, sfCommandOption::PARAMETER_REQUIRED, 'remove orphan assets', false),
      new sfCommandOption('removeOrphanFolders', null, sfCommandOption::PARAMETER_REQUIRED, 'remove orphan folders', false),
    ));

    $this->namespace = 'sfAsset';
    $this->name = 'synchronize';
    $this->briefDescription = 'synchronize a physical folder content with the asset library';

  }

  protected function execute($arguments = array(), $options = array())
  {

    $databaseManager = new sfDatabaseManager($this->configuration);

    $this->logSection('sfAsset', sprintf("Comparing files from %s with assets stored in the database...", $arguments['dirname']));

    $rootFolder = sfAssetFolderTable::getRoot();
    if(!$rootFolder instanceof sfAssetFolder)
    {
      throw new sfException('no root node defined !');
    }
    try
    {
      $rootFolder->synchronizeWith($arguments['dirname'], $options['verbose'], $options['removeOrphanAssets'], $options['removeOrphanFolders']);
    }
    catch (sfAssetException $e)
    {
      throw new sfException(strtr($e->getMessage(), $e->getMessageParams()));
    }

    $this->logSection('sfAsset', "Synchronization complete");
  }
}