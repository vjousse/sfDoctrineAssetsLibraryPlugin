<?php use_helper('sfAsset') ?>
<div id="sf_asset_breadcrumbs">
   <?php echo assets_library_breadcrumb($folder->getRelativePath(ESC_RAW)) ?>
</div>

<?php if (!$folder->getNode()->isRoot()): ?>
  <div class="assetImage">
    <div class="thumbnails">
      <?php echo link_to_asset(image_tag('/swDoctrineAssetsLibraryPlugin/images/up', 'size=64x64 title='.__('Parent directory', null, 'sfAsset')), 'sfAsset/list?dir='. $folder->getParentPath()) ?>
    </div>
    <div class="assetComment" id="ajax_dir_0">..</div>
  </div>
<?php endif; ?>

<?php if($folder->getNode()->hasChildren()): ?>
  <?php foreach ($folder->getNode()->getChildren() as $dir): ?>
    <div class="assetImage">
      <div class="thumbnails">
        <?php echo link_to_asset(
          image_tag('/swDoctrineAssetsLibraryPlugin/images/folder', 'size=64x64 title='.$dir->getName()), 
          '@sf_asset_library_dir?dir='.$dir->getRelativePath()) 
        ?>
      </div>
      <div class="assetComment"><?php echo auto_wrap_text($dir->getName()) ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php foreach ($folder->getsfAssets() as $sf_asset): ?>
  <?php include_partial('sfAsset/asset', array('sf_asset' => $sf_asset, 'folder' => $folder)) ?>
<?php endforeach; ?>