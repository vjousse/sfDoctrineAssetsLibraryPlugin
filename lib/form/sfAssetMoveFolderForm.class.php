<?php


class sfAssetMoveFolderForm extends PluginsfAssetFolderForm
{
  public function configure()
  {
    $root = Doctrine::getTable('sfAssetFolder')->getTree()->fetchRoot();

    unset($this['name']);
    
    $this->widgetSchema['parent_folder'] = new swWidgetFormDoctrineSelectNestedSet(array(
      'object'    => $this->getObject(),
    ));

    $this->validatorSchema['parent_folder'] = new swValidatorDoctrineNestedSet(array(
      'object'    => $this->getObject(),
    ));
  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);

    $parent = $this->getValue('parent_folder');

    $object->getNode()->insertAsLastChildOf($parent);

    return $object;
  }
}