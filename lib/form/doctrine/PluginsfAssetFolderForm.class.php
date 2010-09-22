<?php

/**
 * PluginsfAssetFolder form.
 *
 * @package    form
 * @subpackage sfAssetFolder
 * @version    SVN: $Id: sfPropelFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginsfAssetFolderForm extends BasesfAssetFolderForm
{
  public function setup()
  {
    parent::setup();

    unset($this['created_at']);
    unset($this['updated_at']);
    unset($this['lft']);
    unset($this['rgt']);
    unset($this['relative_path']);
    unset($this['static_scope']);
    unset($this['level']);
   
  }
}