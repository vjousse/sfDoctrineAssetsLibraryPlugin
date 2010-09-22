<?php


class sfAssetMassUploadForm extends sfForm
{

  public function configure()
  {
    $root = Doctrine::getTable('sfAssetFolder')->getTree()->fetchRoot();

    $this->widgetSchema['parent_folder'] = new swWidgetFormDoctrineSelectNestedSet(array(
      'object'    => $root,
      'full_tree' => true
    ));

    $this->validatorSchema['parent_folder'] = new swValidatorDoctrineNestedSet(array(
      'object'    => $root,
      'full_tree' => true
    ));

    $this->widgetSchema->setNameFormat('mass_upload[%s]');

    $form = new sfAssetForm;
    $form->widgetSchema['folder_id'] = new sfWidgetFormInputHidden;

    $this->embedFormForEach('sfAsset', $form, sfConfig::get('app_swDoctrineAssetsLibrary_mass_upload_size', 5));
  }

  public function bind(array $taintedValues = array(), array $taintedFiles = array())
  {
    foreach($taintedValues['sfAsset'] as $key => $val)
    {
      $taintedValues['sfAsset'][$key]['folder_id'] = $taintedValues['parent_folder'];
    }

    parent::bind($taintedValues, $taintedFiles);
  }


  public function save()
  {
    $values = $this->getValues();

    // TODO : find a better way to populate values.
    foreach($values['sfAsset'] as $key => $value)
    {
      $sf_asset = new sfAsset;
      $sf_asset->fromArray($value);
      $sf_asset->setBinaryContent($value['binary_content']);
      $sf_asset->save();
    }
    
  }
}