<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','E-mail');?>*</label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($item->email)?>" />
</div>