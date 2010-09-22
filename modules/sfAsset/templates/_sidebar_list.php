<?php if ($folder->getNode()->isRoot()): ?>
<div class="form-row">
  <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/images.png', 'align=top') ?>
  <?php echo link_to(__('Mass upload', null, 'sfAsset'), 'sfAsset/massUpload') ?>
</div>
<?php endif ?>

<form action="<?php echo url_for('sfAsset/addQuick') ?>" method="POST" enctype="multipart/form-data">
  <?php echo $quickAddForm['folder_id'] ?>
  
  <label for="new_directory">
    <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/filenew.png', 'align=top') ?>
    <a onclick="document.getElementById('input_new_file').style.display='block'; return false;" href="#"><?php echo __('Add a file', null, 'sfAsset'); ?></a>
  </label>
  
  <div id="input_new_file" style="display: none">
    <?php echo $quickAddForm['description']->renderLabel(__('Description',null,'sfAsset')) ?> <br />
    <?php echo $quickAddForm['description'] ?> <br />
    
    <?php echo $quickAddForm['author']->renderLabel(__('Author',null,'sfAsset')) ?> <br />
    <?php echo $quickAddForm['author'] ?> <br />
    
    <?php echo $quickAddForm['copyright']->renderLabel(__('Copyright',null,'sfAsset')) ?> <br />
    <?php echo $quickAddForm['copyright'] ?> <br />
    
    <?php echo $quickAddForm['binary_content']->renderLabel(__('Binary content',null,'sfAsset')) ?> <br />
    <?php echo $quickAddForm['binary_content'] ?> <br />

    <input type="submit" name="add" value="<?php echo __('Add', null, 'sfAsset') ?>"/>
  </div>
<?php
if(isset($quickAddForm[sfForm::getCSRFFieldName()])) {
echo $quickAddForm['_csrf_token'];
}
?>
</form>


<form action="<?php echo url_for('sfAsset/createFolder') ?>" method="POST">
  <label for="new_directory">
    <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/folder_add.png', 'align=top') ?>
    <a onclick="document.getElementById('input_new_directory').style.display='block'; return false;" href="#"><?php echo __('Add a subfolder', null, 'sfAsset'); ?></a>
  </label>
  <div id="input_new_directory" style="display: none">
    <table>
      <tr>
        <th>
          <?php echo $createFolderForm['name']->renderLabel(__('Name',null,'sfAsset')) ?>
        </th>
        <td>
          <?php echo $createFolderForm['name'] ?>
        </td>
      </tr>
      <tr>
        <th>
          <?php echo $createFolderForm['parent_folder']->renderLabel(__('Parent folder',null,'sfAsset')) ?>
        </th>
        <td>
          <?php echo $createFolderForm['parent_folder'] ?><br />
        </td>
      </tr>    </table>
    <input type="submit" name="create" value="<?php echo __('Create', null, 'sfAsset') ?>"/>
  </div>
<?php
if(isset($createFolderForm[sfForm::getCSRFFieldName()])) {
echo $createFolderForm['_csrf_token'];
}
?>
</form>
    
<?php if (!$folder->getNode()->isRoot()): ?>

  <form action="<?php echo url_for('sfAsset/renameFolder?id='.$folder->getId()) ?>" method="POST">
    <label for="new_directory">
      <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/folder_edit.png', 'align=top') ?>
      <a onclick="document.getElementById('input_new_name').style.display='block'; return false;" href="#"><?php echo __('Rename folder', null, 'sfAsset'); ?></a>
    </label>
    <div class="content" id="input_new_name" style="display: none">
      <table>
        <?php echo $renameFolderForm ?>
      </table>
      <input type="submit" name="create" value="<?php echo __('Ok', null, 'sfAsset') ?>"/>
    </div>
  </form>
  
  <form action="<?php echo url_for('sfAsset/moveFolder?id='.$folder->getId()) ?>" method="POST">
    <label for="new_directory">
      <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/folder_edit.png', 'align=top') ?>
      <a onclick="document.getElementById('input_move_folder').style.display='block'; return false;" href="#"><?php echo __('Move folder', null, 'sfAsset'); ?></a>
     
    </label>
    <div class="content" id="input_move_folder" style="display: none">
      <table>
        <?php echo $moveFolderForm ?>
      </table>
      <input type="submit" name="create" value="<?php echo __('Ok', null, 'sfAsset') ?>"/>
    </div>
  </form>

  <div class="form-row">
    <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/folder_delete.png', 'align=top') ?>
    <?php echo link_to(__('Delete folder', null, 'sfAsset'), 'sfAsset/deleteFolder?id='.$folder->getId(), array(
      'post' => true,
      'confirm' => __('Are you sure?', null, 'sfAsset'),
    )) ?>
  </div>

<?php endif; ?>
