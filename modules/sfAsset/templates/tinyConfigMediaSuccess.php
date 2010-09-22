<?php use_helper('Javascript', 'I18N', 'sfAsset') ?>
<p><?php echo button_to_function(__('Back to the list', null, 'sfAsset'), 'history.back()') ?></p>

<script src="/js/tiny_mce/tiny_mce_popup.js"></script>
<script src="/js/tiny_mce/plugins/swDoctrineAssetsLibraryPlugin/jscripts/sfAssetsLibrary.js"></script>
<?php echo form_tag('', 'id=tinyMCE_insert_form') ?>
  <fieldset>
    <?php echo asset_image_tag($sf_asset, 'large', array('class' => 'thumb')) ?>

    <div class="form-row">
      <label><?php echo __('Filename:', null, 'sfAsset'); ?></label>
      <div class=""><?php echo $sf_asset->getUrl() ?></div>
    </div>

    <div class="form-row">
      <label><?php echo __('Copyright:'); ?></label>
      <div class="content"><?php echo input_tag('alt'.$sf_asset->getId(), $sf_asset->getCopyright(), 'size=80') ?></div>
    </div>

    <div class="form-row">
      <label><?php echo __('Image size:', null, 'sfAsset'); ?></label>
      <?php
      list($widthoriginal, $heightoriginal, $type, $attr) = getimagesize(sfConfig::get('sf_web_dir').$sf_asset->getUrl());
      list($widthsmall, $heightsmall, $type, $attr)       = getimagesize(sfConfig::get('sf_web_dir').$sf_asset->getUrl('small'));
      list($widthlarge, $heightlarge, $type, $attr)       = getimagesize(sfConfig::get('sf_web_dir').$sf_asset->getUrl('large'));
      ?>
      <?php echo input_hidden_tag('height0_'.$sf_asset->getId(), $heightoriginal) ?>
      <?php echo input_hidden_tag('width0_'.$sf_asset->getId(), $widthoriginal) ?>
      
      <?php echo input_hidden_tag('height1_'.$sf_asset->getId(), $heightlarge) ?>
      <?php echo input_hidden_tag('width1_'.$sf_asset->getId(), $widthlarge) ?>
      
      <?php echo input_hidden_tag('height2_'.$sf_asset->getId(), $heightsmall) ?>
      <?php echo input_hidden_tag('width2_'.$sf_asset->getId(), $widthsmall) ?>
                      
      <div class="content">
        <?php echo select_tag('thumbnails'.$sf_asset->getId(),
          array(__('Original', null, 'sfAsset'), __('Large thumbnail', null, 'sfAsset'), __('Small thumbnail', null, 'sfAsset')),
          array('onchange' => 
            "var selectedWidthId  = 'width'  + $(this).selectedIndex + '_".$sf_asset->getId()."';" .
            "var selectedHeightId = 'height' + $(this).selectedIndex + '_".$sf_asset->getId()."';" .
            "$('height".$sf_asset->getId()."').value = $(selectedHeightId).value;" .
            "$('width".$sf_asset->getId()."').value  = $(selectedWidthId).value;"
          )) ?>
      </div>
    </div>
    
    <div class="form-row">
      <label><?php echo __('Frame image', null, 'sfAsset') ?></label>
      <div class="content"><?php echo checkbox_tag('border'.$sf_asset->getId(), 1, true, array(
        'onclick' => 'document.getElementById(\'frame_fieldset\').style.display = this.checked ? "block" : "none";')) ?></div>
    </div>
    
    <fieldset id="frame_fieldset">

    <div class="form-row">
      <label><?php echo __('Display description', null, 'sfAsset') ?></label>
      <div class="content"><?php echo checkbox_tag('legend'.$sf_asset->getId(), 1, true, array(
        'onclick' => 'document.getElementById(\'legend_form_row\').style.display = this.checked ? "block" : "none";')) ?></div>
    </div>

    <div class="form-row" id="legend_form_row">
      <label><?php echo __('Description:', null, 'sfAsset') ?></label>
      <div class=""><?php echo input_tag('description'.$sf_asset->getId(), $sf_asset->getDescription(), 'size=80') ?></div>
    </div>
    
    <div class="form-row">
      <label><?php echo __('Image align:', null, 'sfAsset') ?></label>
      <div class="content"><?php echo select_tag('align'.$sf_asset->getId(), array(
        'left'   => 'gauche',
        'center' => 'centre',
        'right'  => 'droite'
      )) ?></div>
    </div>
    
    <div class="form-row">
      <label><?php echo __('Width (%):', null, 'sfAsset') ?></label>
      <div class="content"><?php echo input_tag('width'.$sf_asset->getId(), 50, 'size=5') ?></div>
    </div>

    </fieldset>
    
    <ul class="sf_admin_actions">
      <li>
        <?php echo button_to_function(__('Insert', null, 'sfAsset'),
         "insertAction(
           '".$sf_asset->getUrl()."', 
           $('alt".$sf_asset->getId()."').value,
           $('border".$sf_asset->getId()."').checked,
           $('legend".$sf_asset->getId()."').checked,
           $('description".$sf_asset->getId()."').value,
           $('align".$sf_asset->getId()."').value,
           $('thumbnails".$sf_asset->getId()."').selectedIndex,
           $('width".$sf_asset->getId()."').value
          )",'class=sf_admin_action_save') ?>
      </li>
    </ul>
  </fieldset>
</form>