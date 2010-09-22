<?php

class sfAssetsDatagrid extends BasesfAssetsDatagrid
{
  public function getModelName()
  {
    return "sfAssetFolder";
  }

  public function setupDatagrid()
  {
  
    $this->addFilter(
      'name',
      null,
      new sfWidgetFormInput(),
      new sfValidatorString(array('max_length' => 255, 'required' => false))
    );
  }

  function buildQuery(Doctrine_Query $query) {

    if($this->getValue('name'))
    {
      $query->andWhere('name LIKE ?', '%'.$this->getValue('name').'%');
    }
  
    return $query;
  }
}