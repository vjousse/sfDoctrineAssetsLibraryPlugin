<?php

/**
 * PluginsfAsset form.
 *
 * @package    form
 * @subpackage sfAsset
 * @version    SVN: $Id: sfPropelFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginsfAssetForm extends BasesfAssetForm
{
  
  public function setup()
  {
    parent::setup();

    $this->widgetSchema['binary_content']    = new sfWidgetFormInputFile;
    $this->validatorSchema['binary_content'] = new sfValidatorFile(array(
      'required' => false
    ));
    
    unset(
      $this['created_at'],
      $this['updated_at'],
      $this['filesize'],
      $this['type'],
      $this['filename']
    );
  }
  
  public function updateObject($values = null)
  {
    
    $object = parent::updateObject($values);

    if(array_key_exists('binary_content', $this->values))
    {
      $object->setBinaryContent($this->values['binary_content']);
    }
    
    return $object;
  }
}