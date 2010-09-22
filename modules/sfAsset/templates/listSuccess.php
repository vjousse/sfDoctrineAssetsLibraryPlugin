<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_helper('I18N')?>

<h1><?php echo __('Asset Library', null, 'sfAsset') ?></h1>

<?php if (!$popup) : ?>
  <?php include_partial('list_header', array('folder' => $folder)) ?>
<?php endif; ?>

<div id="sf_asset_bar">
  <?php /*
  <p><?php echo $folder->getName() ?></p>
  <?php if ($foldersList->count() || $assetsList->count()): ?>
    <?php if ($foldersList->count() > 0): ?>
      <p><?php echo format_number_choice('[1]One subfolder|(1,+Inf)%nb% subfolders', array('%nb%' => $foldersList->count()), $foldersList->count(), 'sfAsset')  ?></p>
    <?php endif; ?>
    <?php if ($assetsList->count() > 0): ?>
      <p><?php  echo format_number_choice('[1]One file, %weight% Kb|(1,+Inf)%nb% files, %weight% Kb', array('%nb%' => $assetsList->count(), '%weight%' => '0'), $assetsList->count(), 'sfAsset') ?></p>
    <?php endif; ?>
  <?php endif; ?>
*/ ?> 
  <?php include_partial('sfAsset/sidebar_sort') ?>
  <?php include_partial('sfAsset/sidebar_search', array('searchDatagrid' => $searchDatagrid)) ?>
  <?php include_partial('sfAsset/sidebar_list', array(
    'folder'           => $folder, 
    'quickAddForm'     => $quickAddForm,
    'createFolderForm' => $createFolderForm,
    'renameFolderForm' => $renameFolderForm,
    'moveFolderForm'   => $moveFolderForm,
  
  )) ?>
</div>

<div id="sf_asset_container">
  <?php include_partial('sfAsset/messages') ?>
  <?php include_partial('sfAsset/list', array(
    'folder' => $folder,
    'dirs'   => $folder->getNode()->getChildren(),
    'files'  => $folder->getsfAssets()
  )) ?>
</div>

<?php if (!$popup) : ?>
  <?php include_partial('sfAsset/list_footer', array('folder' => $folder)) ?>
<?php endif; ?>