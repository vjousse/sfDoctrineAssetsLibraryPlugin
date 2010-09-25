<?php use_helper('sfAsset') ?>
<div class="form-row">
  <?php $sort = $sf_user->getAttribute('sort', 'name', 'sf_admin/sf_asset/sort') ?>
  <label>
    <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/text_linespacing.png', 'align=top') ?>
    <?php if ($sort == 'name'): ?>
      <?php echo __('Sorted by name', null, 'sfAsset') ?>
      <?php echo link_to_asset(__('Sort by date', null, 'sfAsset'), 'sfAsset/'.$sf_params->get('action').'?dir='.$sf_params->get('dir'), array('query_string' => 'sort=date')) ?>
    <?php else: ?>
      <?php echo __('Sorted by date', null, 'sfAsset') ?>
      <?php echo link_to_asset(__('Sort by name', null, 'sfAsset'), 'sfAsset/'.$sf_params->get('action').'?dir='.$sf_params->get('dir'), array('query_string' => 'sort=name')) ?>
    <?php endif ?>
  </label>
</div>