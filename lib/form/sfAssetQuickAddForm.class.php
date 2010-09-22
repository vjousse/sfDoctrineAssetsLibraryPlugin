<?php

class sfAssetQuickAddForm extends sfAssetForm
{
  
  public function setup()
  {
    parent::setup();
    
    $this->validatorSchema['folder_id']->setOption('required', true);
    
    if($this->getOption('folder') instanceof sfAssetFolder)
    {
      $this->widgetSchema['folder_id'] = new sfWidgetFormInputHidden;
      $defaults = $this->getDefaults();
      $defaults['folder_id'] = $this->getOption('folder')->getId();
      $this->setDefaults($defaults);
    }
    
    
  }
}