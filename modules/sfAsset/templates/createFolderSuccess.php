<?php use_helper('Javascript') ?>
<?php use_helper('Validation', 'I18N') ?>

<h1><?php echo __('Create a new folder', null, 'sfAsset') ?></h1>

<?php include_partial('sfAsset/create_folder_header') ?>

<form action="<?php echo url_for('sfAsset/createFolder') ?>" method="POST">
  <label for="new_directory">
    <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/folder_add.png', 'align=top') ?>
    <a onclick="document.getElementById('input_new_directory').style.display='block'; return false;" href="#"><?php echo __('Add a subfolder', null, 'sfAsset'); ?></a>

  </label>
  <div id="input_new_directory" style="display: none">
    <table>
      <?php echo $createFolderForm ?>
    </table>
    <input type="submit" name="create" value="<?php echo __('Create', null, 'sfAsset') ?>"/>
  </div>
</form>

<?php include_partial('sfAsset/create_folder_footer') ?>