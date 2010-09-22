<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormJQueryDate represents a date widget rendered by JQuery UI.
 *
 * This widget needs JQuery and JQuery UI to work.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Vincent Jousse <vincent.jousse@devorigin.fr>
 */
class sfWidgetFormJQueryAssetSelect extends sfWidgetForm
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * image:   The image path to represent the widget (false by default)
   *  * config:  A JavaScript array that configures the JQuery date widget
   *  * culture: The user culture
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('many', false);
    $this->addRequiredOption('assets');


    parent::configure($options, $attributes);

  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (!is_array($value))
    {
      $value = array($value);
    }

    $assets = $this->getOption('assets');


    if ($assets instanceof sfAsset)
    {
      $assets = array($assets);
    }


    //var_dump($this->getOption('assets'));exit;

    $prefix = $this->generateId($name);
    $html='
    <style type="text/css">
    .sortableJ{
    margin:10px 0px 20px 0px;
    padding:0px;
    width:100%;
    }

    .sortableJ li{
    float:left;
    list-style:none;
    display:block;
    padding: 5px 5px 5px 5px;
    text-align: center;
    }

    </style>

    <script type="text/javascript">
    ';

    if($this->getOption('many')) $many=1; else $many=0;

    if($many) $text = 'Ajouter un autre média'; else $text= 'Choisir un média';

    $html.='
      var assetsFormName=\'' . $name . '\';
      var isMany' . $prefix . '=' .  $many . ';


      $(document).ready(function(){';
      foreach($assets as $asset)
      {

        $html.='addAssetToTab(\'' . $prefix . '\',' . $asset->id . ',\''.$asset->getImageSrc('small').'\');';
      }
        
    $html.='
      });
    </script>
    
    ' . link_to($text,'sfAsset/list?popup=4&widgetid='.$prefix,array('class'=>'iframe')).'
    
    <div id="assets_content_' . $prefix . '">
      <ul class="sortableJ"></ul>
    </div>

    ';

    return $html;

  }
}
