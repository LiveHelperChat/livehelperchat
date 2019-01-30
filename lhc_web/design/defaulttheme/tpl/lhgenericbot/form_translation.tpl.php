<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>