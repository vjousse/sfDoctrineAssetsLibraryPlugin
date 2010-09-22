<?php

class sfAssetCreateFolderForm extends sfAssetFolderForm
{

  public function configure()
  {
    $root = Doctrine::getTable('sfAssetFolder')->getTree()->fetchRoot();

    unset($this['folder_id']);
     
    $this->widgetSchema['parent_folder'] = new swWidgetFormDoctrineSelectNestedSet(array(
      'object'    => $root,
      'full_tree' => true
    ));

    $this->validatorSchema['parent_folder'] = new swValidatorDoctrineNestedSet(array(
      'object'    => $root,
      'full_tree' => true
    ));

    if($this->getOption('folder') instanceof sfAssetFolder)
    {
      $defaults = $this->getDefaults();
      $defaults['parent_folder'] = $this->getOption('folder')->getId();
      $this->setDefaults($defaults);
    }

  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);

    $parent = $this->getValue('parent_folder');

    $object->getNode()->insertAsLastChildOf($parent);

    return $object;
  }
}