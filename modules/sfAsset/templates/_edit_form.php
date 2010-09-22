<?php use_helper('sfAsset'); ?>

<?php if ($form->hasGlobalErrors()): ?>

        <ul class="error_list">
          <?php foreach ($form->getGlobalErrors() as $name => $error): ?>
            <li><?php echo $name.': '.$error ?></li>
          <?php endforeach; ?>
        </ul>

<?php endif; ?>

<form action="<?php echo url_for('sfAsset/update?id='.$sf_asset->getId()) ?>" method="post" enctype="multipart/form-data">

<fieldset id="sf_fieldset_none" class="">

  <div class="form-row">
    <div class="content">
    <?php echo __('Path:', null, 'sfAsset') ?>
    <?php if (!$sf_asset->isNew()): ?>
      <?php echo assets_library_breadcrumb($sf_asset->getFolderPath(ESC_RAW), true, 'list') ?><?php echo $sf_asset->getFilename(); ?>
    <?php endif ?>
    </div>
  </div>

</fieldset>

<fieldset id="sf_fieldset_meta" class="">
  <table>
  <?php echo $form ?>
  </table>
  <input type="submit" name="submit" />
</fieldset>
</form>