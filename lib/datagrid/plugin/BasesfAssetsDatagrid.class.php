<?php

class sfAssetsDatagrid extends BasesfAssetsDatagrid
{
    public function getModelName()
  {
    return "sfAsset";
  }

  public function setupDatagrid()
  {
    $this->addFilter(
      'folder_id',
      null,
      new sfWidgetFormDoctrineSelect(array('model' => 'sfAssetFolder', 'add_empty' => true)),
      new sfValidatorDoctrineChoice(array('model' => 'sfAssetFolder', 'required' => false))
    );

    $this->addFilter(
      'filename',
      null,
      new sfWidgetFormInput(),
      new sfValidatorString(array('max_length' => 255, 'required' => false))
    );

    $this->addFilter(
      'description',
      null,
      new sfWidgetFormInput(),
      new sfValidatorString(array('max_length' => 255, 'required' => false))
    );
    
    $this->addFilter(
      'author',
      null,
      new sfWidgetFormInput(),
      new sfValidatorString(array('max_length' => 255, 'required' => false))
    );
    
    $this->addFilter(
      'copyright',
      null,
      new sfWidgetFormInput(),
      new sfValidatorString(array('max_length' => 255, 'required' => false))
    );
    
    $this->widgetSchema->setNameFormat('search_filters[%s]');
  }

  function buildQuery(Doctrine_Query $query) {

    if($this->getValue('folder_id'))
    {
      $query->andWhere('folder_id = ?', $this->getValue('folder_id'));
    }

    if($this->getValue('filename'))
    {
      $query->andWhere('filename LIKE ?', '%'.$this->getValue('filename').'%');
    }

    if($this->getValue('author'))
    {
      $query->andWhere('author LIKE ?', '%'.$this->getValue('author').'%');
    }
    
    if($this->getValue('description'))
    {
      $query->andWhere('description LIKE ?', '%'.$this->getValue('description').'%');
    }

    if($this->getValue('copyright'))
    {
      $query->andWhere('copyright LIKE ?', '%'.$this->getValue('copyright').'%');
    }
    
    return $query;
  }
}