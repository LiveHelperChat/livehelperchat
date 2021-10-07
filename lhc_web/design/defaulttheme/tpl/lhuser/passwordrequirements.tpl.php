<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Password requirements')?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form ng-non-bindable action="" method="post" autocomplete="off">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','User has to change password every n days');?></label>
        <input type="number" class="form-control" name="expires_in" value="<?php (isset($password_data['expires_in'])) ? print htmlspecialchars($password_data['expires_in']) : print '' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Minimal password length');?></label>
        <input type="number" class="form-control" name="length" value="<?php (isset($password_data['length'])) ? print htmlspecialchars($password_data['length']) : print '' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Maximum number of failed logins before account is disabled.');?></label>
        <input type="number" class="form-control" name="max_attempts" value="<?php (isset($password_data['max_attempts'])) ? print htmlspecialchars($password_data['max_attempts']) : print '' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Uppercase letters required');?></label>
        <input type="number" class="form-control" name="uppercase_required" value="<?php (isset($password_data['uppercase_required'])) ? print htmlspecialchars($password_data['uppercase_required']) : print '0' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Lowercase characters required');?></label>
        <input type="number" class="form-control" name="lowercase_required" value="<?php (isset($password_data['lowercase_required'])) ? print htmlspecialchars($password_data['lowercase_required']) : print '0' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Numbers required');?></label>
        <input type="number" class="form-control" name="number_required" value="<?php (isset($password_data['number_required'])) ? print htmlspecialchars($password_data['number_required']) : print '0' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Special characters required');?></label>
        <input type="number" class="form-control" name="special_required" value="<?php (isset($password_data['special_required'])) ? print htmlspecialchars($password_data['special_required']) : print '0' ?>" />
    </div>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="StorePasswordSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
    </div>
</form>