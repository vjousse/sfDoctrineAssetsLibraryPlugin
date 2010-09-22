<?php

class BasesfAssetActions extends sfActions
{
  public function executeIndex()
  {
    $this->getUser()->getAttributeHolder()->remove('popup', null, 'sf_admin/sf_asset/navigation');
    $this->redirect('sfAsset/list');
  }

  public function executeList($request)
  {
    $folder = sfAssetFolderTable::retrieveByPath($this->getRequestParameter('dir'));

    if(!$folder)
    {
      if ($this->getUser()->getFlash('sfAsset_folder_not_found'))
      {
        throw new sfException('You must create a root folder. Use the `php symfony sfAsset:create-root` command for that.');
      }
      else
      {
        if ($popup = $this->getRequestParameter('popup'))
        {
          $this->getUser()->setAttribute('popup', $popup, 'sf_admin/sf_asset/navigation');
        }

        //Used by the jquery widget
        if ($request->getParameter('widgetid'))
        {
          $this->getUser()->setAttribute('widgetid', $request->getParameter('widgetid'), 'sf_admin/sf_asset/navigation');

        }
        $this->getUser()->setFlash('sfAsset_folder_not_found', true);
        $this->redirect('sfAsset/list');
      }
    }

    //Used by the jquery widget
    if ($request->getParameter('widgetid'))
    {
      $this->getUser()->setAttribute('widgetid', $request->getParameter('widgetid'), 'sf_admin/sf_asset/navigation');

    }

    $this->folder = $folder;

    // search option
    $this->searchDatagrid = new sfAssetDatagrid(
      $request->getParameter('search_filters', array())
    );

    // quick add option
    $this->quickAddForm = new sfAssetQuickAddForm(
      new sfAsset,
      array('folder' => $folder)
    );

    // create folder
    $this->createFolderForm = new sfAssetCreateFolderForm(
      new sfAssetFolder,
      array('folder' => $folder)
    );
    
    // rename folder
    $this->renameFolderForm = new sfAssetFolderForm($folder);
    
    // move folder
    $this->moveFolderForm = new sfAssetMoveFolderForm($folder);
    
    $this->removeLayoutIfPopup();

  }

  public function executeSearch($request)
  {
    $this->searchDatagrid = new sfAssetDatagrid(
      $request->getParameter('search_filters')
    );

    $this->removeLayoutIfPopup();
  }

  public function executeCreateFolder($request)
  {
    $form = new sfAssetCreateFolderForm;

    if($request->isMethod('POST') && $form->bindAndSave($request->getParameter('sf_asset_folder')))
    {
      $this->redirectToPath('sfAsset/list?dir='.$form->getObject()->getRelativePath());
    }

    $this->createFolderForm = $form;
  }

  public function executeMoveFolder($request)
  {
    
    $sf_asset_folder = Doctrine::getTable('sfAssetFolder')->find($request->getParameter('id'));
    
    $this->forward404if(!$request->isMethod('POST'));
    $this->forward404if(!$sf_asset_folder);
    
    // TODO : Try to find a better way of doing that, if possible
    $form = new sfAssetMoveFolderForm($sf_asset_folder);
    $form->bind($request->getParameter('sf_asset_folder'));
    $target_folder = $form->getValue('parent_folder');

    try
    {
      $sf_asset_folder->move($target_folder);
      $this->getUser()->setFlash('notice', 'The folder has been moved');
    }
    catch (sfAssetException $e)
    {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
    }

    return $this->redirectToPath('sfAsset/list?dir=' . $sf_asset_folder->getRelativePath());
  }

  public function executeRenameFolder($request)
  {
    
    $sf_asset_folder = Doctrine::getTable('sfAssetFolder')->find($request->getParameter('id'));
      
    $this->forward404if(!$sf_asset_folder);
    
    $form = new sfAssetFolderForm($sf_asset_folder);
    
    if($request->isMethod('POST'))
    {
      try {
        
        if($form->bindAndSave($request->getParameter('sf_asset_folder')))
        {
          $this->getUser()->setFlash('notice', 'The folder has been renamed');
          $this->redirectToPath('sfAsset/list?dir='.$form->getObject()->getRelativePath());
          
        }
        else
        {
          
        }
      }
      catch (sfAssetException $e)
      {
  
        $this->getUser()->setFlash('warning_message', $e->getMessage());
        $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      }
    }

    return $this->redirectToPath('sfAsset/list?dir=' . $form->getObject()->getRelativePath());
  }

  public function executeDeleteFolder($request)
  {
    $this->forward404Unless($request->isMethod('POST'));
    $sf_asset_folder = Doctrine::getTable('sfAssetFolder')->find($request->getParameter('id'));
    
    $this->forward404if(!$sf_asset_folder);
    
    try
    {
      $sf_asset_folder->delete();
      $this->getUser()->setFlash('notice', 'The folder has been deleted');
    }
    catch (sfAssetException $e)
    {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
    }

    return $this->redirectToPath('sfAsset/list?dir=' . $sf_asset_folder->getParentPath());
  }

  public function executeAddQuick($request)
  {
    $quickAddForm = new sfAssetQuickAddForm(
      new sfAsset
    );

    if($request->isMethod('POST'))
    {
      $quickAddForm->bind($request->getParameter('sf_asset'), $request->getFiles('sf_asset'));

      if($quickAddForm->isValid())
      {
        try
        {
          $quickAddForm->save();
          $sf_asset_folder = $quickAddForm->getObject()->getsfAssetFolder();
          
          $this->redirectToPath('sfAsset/list?dir='.$sf_asset_folder->getRelativePath());
        }
        catch(sfAssetException $e)
        {
          $this->getUser()->setFlash('warning_message', $e->getMessage());
          $this->getUser()->setFlash('warning_params', $e->getMessageParams());
        }
      }
    }

    $asset = $quickAddForm->getObject();
    $folder = $asset->getsfAssetFolder();

    $this->forward404Unless($folder);

    if($this->getUser()->hasAttribute('popup', 'sf_admin/sf_asset/navigation'))
    {
      if($this->getUser()->getAttribute('popup', null, 'sf_admin/sf_asset/navigation') == 1)
      {
        $this->redirect('sfAsset/tinyConfigMedia?id='.$asset->getId());
      }
      else
      {
        $this->redirectToPath('sfAsset/list?dir='.$folder->getRelativePath());
      }
    }
    $this->redirect('sfAsset/edit?id='.$asset->getId());
  }

  public function executeMassUpload($request)
  {
    $form = new sfAssetMassUploadForm();

    if($request->isMethod('POST'))
    {
      $form->bind($request->getParameter('mass_upload'), $request->getFiles('mass_upload'));

      if($form->isValid())
      {
        $form->save();

        $this->getUser()->setFlash('notice', 'Files successfully uploaded');

        $form = new sfAssetMassUploadForm;
      }
    }

    $this->form = $form;

  }

  public function executeDeleteAsset()
  {
    
    $sf_asset = Doctrine::getTable('sfAsset')->find($this->getRequestParameter('id'));
    $this->forward404Unless($sf_asset);
    $folderPath = $sf_asset->getFolderPath();
    try
    {
      $sf_asset->delete();
    }
    catch (sfDoctrineException $e)
    {
      $this->getRequest()->setError('delete', 'Impossible to delete asset, probably due to related records');
      return $this->forward('sfAsset', 'edit');
    }

    return $this->redirectToPath('sfAsset/list?dir='.$folderPath);
  }

  public function executeCreate()
  {
    return $this->forward('sfAsset', 'edit');
  }

  public function executeEdit($request)
  {
    $this->sf_asset = $this->getsfAssetOrCreate();

    $form = new sfAssetForm($this->sf_asset);

    $this->form = $form;
  }

  public function executeUpdate($request)
  {
    $this->sf_asset = $this->getsfAssetOrCreate();

    $form = new sfAssetForm($this->sf_asset);

    if($request->isMethod('POST'))
    {
      $form->bind($request->getParameter('sf_asset'), $request->getFiles('sf_asset'));
      if($form->isValid())
      {
        $form->save();
        $this->getUser()->setFlash('notice', 'Your modifications have been saved');
        
        return $this->redirect('sfAsset/edit?id='.$this->sf_asset->getId());
      }
    }
  }

  protected function removeLayoutIfPopup()
  {
    if($popup = $this->getRequestParameter('popup'))
    {
      $this->getUser()->setAttribute('popup', $popup, 'sf_admin/sf_asset/navigation');
    }
   
    if($this->getUser()->hasAttribute('popup', 'sf_admin/sf_asset/navigation'))
    {
      $this->getResponse()->addJavascript('/js/tiny_mce/tiny_mce_popup.js');
      $this->setLayout($this->getContext()->getConfiguration()->getTemplateDir('sfAsset', 'popupLayout.php').DIRECTORY_SEPARATOR.'popupLayout');
      $this->popup = true;
    }
    else
    {
      $this->popup = false;
    }
  }

  protected function getsfAssetOrCreate($id = 'id')
  {
    if (!$this->getRequestParameter($id))
    {
      $sf_asset = new sfAsset;
    }
    else
    {
      $sf_asset = Doctrine::getTable('sfAsset')->find($this->getRequestParameter($id));

      $this->forward404Unless($sf_asset);
    }

    return $sf_asset;
  }

  protected function redirectToPath($path, $statusCode = 302)
  {
    $url = $this->getController()->genUrl($path, true);
    $url = str_replace('%2F', '/', $url);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} redirect to "'.$url.'"');
    }

    $this->getController()->redirect($url, 0, $statusCode);

    throw new sfStopException();
  }

  public function executeTinyConfigMedia()
  {
    $this->forward404Unless($this->hasRequestParameter('id'));
    $this->sf_asset = sfAssetPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->sf_asset);

    $this->setLayout(sfLoader::getTemplateDir('sfAsset', 'popupLayout.php').'/popupLayout');

    return sfView::SUCCESS;
  }
}