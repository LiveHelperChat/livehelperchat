<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control form-control-sm" name="Name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
     <label><input type="checkbox" name="Type" value="1" <?php if ($item->type == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Private');?></label>
</div>
