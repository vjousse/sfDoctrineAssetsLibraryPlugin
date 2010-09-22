<?php if(isset($sf_asset)): ?>
  <?php //echo content_tag('a', $thumbnail, array('href' => $web_abs_current_path.'/'.$name, 'target' => '_blank')) ?>
  <?php echo image_tag($sf_asset->getPath()) ?>  
<?php endif;