<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control form-control-sm" name="Name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>
