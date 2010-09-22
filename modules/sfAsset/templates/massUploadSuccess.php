<?php use_helper('I18N') ?>

<h1><?php echo __('Mass upload files', null, 'sfAsset') ?></h1>

<?php include_partial('sfAsset/create_folder_header') ?>

<form action="<?php echo url_for('sfAsset/massUpload') ?>" method="POST" enctype="multipart/form-data">
  <?php echo $form ?>
  <input type="submit" />
</form>

<?php include_partial('sfAsset/create_folder_footer') ?>