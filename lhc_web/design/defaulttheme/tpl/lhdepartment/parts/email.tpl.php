<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E-mail');?></label>
    <input type="text" class="form-control form-control-sm" name="Email"  value="<?php echo htmlspecialchars($departament->email);?>" />
</div>