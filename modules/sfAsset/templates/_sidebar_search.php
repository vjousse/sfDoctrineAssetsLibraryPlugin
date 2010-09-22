<div class="form-row">
  <?php echo image_tag('/swDoctrineAssetsLibraryPlugin/images/magnifier.png', 'align=top') ?>
  <a onclick="document.getElementById('sf_asset_search').style.display='block'; return false;" href="#"><?php echo __('Search', null, 'sfAsset'); ?></a>
</div>

<form action="<?php echo url_for('sfAsset/search') ?>" id="sf_asset_search" style="display:none" >
  <dl>
  
    <?php echo $searchDatagrid ?>
  </dl>
  
  <ul class="sf_admin_actions">
    <input type="submit" name="search" class="sf_admin_action_filter" value="<?php echo __('Search', null, 'sfAsset') ?>"/>
  </ul>
</form>