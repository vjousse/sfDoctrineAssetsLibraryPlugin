<?php use_helper('sfAsset') ?>
<div class="assetImage">
  <div class="thumbnails">
    <?php echo link_to_asset_action(asset_image_tag($sf_asset, 'small', array('width' => 84), isset($folder) ? $folder->getRelativePath() : null), $sf_asset) ?>
  </div>

  <div class="assetComment">
    <?php echo auto_wrap_text($sf_asset->getFilename()) ?>
    <div class="details">
      <?php echo $sf_asset->getFilesize() ?> Ko
      <?php if (!$sf_user->hasAttribute('popup', 'sf_admin/sf_asset/navigation')): ?>
        <?php echo link_to(image_tag('/swDoctrineAssetsLibraryPlugin/images/delete.png', 'class=deleteImage align=top'), 'sfAsset/deleteAsset?id='.$sf_asset->getId(), array('title' => __('Supprimer'), 'confirm' => __('Etes-vous sûr ?'))); ?>
      <?php endif; ?>
    </div>
  </div>
</div>
