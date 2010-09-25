<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardRouting.class.php 7636 2008-02-27 18:50:43Z fabien $
 */
class sfAssetsRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    // preprend our routes
    $r->appendRoute(
      'sf_asset_library_dir', 
      new sfRoute(
        '/sfAsset/list', 
        array(
          'module'    => 'sfAsset',
          'action'    => 'list',
          //'dir'       => sfConfig::get('app_sfDoctrineAssetsLibrary_upload_dir', 'media')
        ),
        array(),
        array(
          'extra_parameters_as_query_string' => true
        )
      )
    );
  }
}