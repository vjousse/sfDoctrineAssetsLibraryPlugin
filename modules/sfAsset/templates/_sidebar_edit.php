<?php use_helper('JavascriptBase', 'sfAsset') ?>

<?php if (!$sf_asset->isNew()): ?>
  <div id="thumbnail">
    <a href="<?php echo $sf_asset->getUrl('full') ?>"><?php echo asset_image_tag($sf_asset, 'large', array('title' => __('See full-size version', null, 'sfAsset')), null) ?></a>
  </div>
  <p><?php echo auto_wrap_text($sf_asset->getFilename()) ?></p>
  <p><?php echo __('%weight% Kb', array('%weight%' => $sf_asset->getFilesize()), 'sfAsset') ?></p>
  <p><?php echo __('Created on %date%', array('%date%' => format_date($sf_asset->getCreatedAt('U'))), 'sfAsset') ?></p>

<?php endif ?>
