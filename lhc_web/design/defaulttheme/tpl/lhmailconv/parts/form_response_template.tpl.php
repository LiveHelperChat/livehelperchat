<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Name');?></label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Template');?></label>
    <textarea class="form-control form-control-sm" name="template"><?php echo htmlspecialchars($item->template)?></textarea>
</div>