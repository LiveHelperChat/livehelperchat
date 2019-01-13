<h1>Password requirements</h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','User has to change password every n days');?></label>
        <input type="text" class="form-control" name="expires_in" value="<?php (isset($password_data['expires_in'])) ? print htmlspecialchars($password_data['expires_in']) : print '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Minimal password length');?></label>
        <input type="text" class="form-control" name="length" value="<?php (isset($password_data['length'])) ? print htmlspecialchars($password_data['length']) : print '' ?>" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="uppercase_required" value="on" <?php (isset($password_data['uppercase_required']) && $password_data['uppercase_required'] == 1) ? print 'checked="checked"' : print '' ?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Uppercase letter required');?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="number_required" value="on" <?php (isset($password_data['number_required']) && $password_data['number_required'] == 1) ? print 'checked="checked"' : print '' ?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Number required');?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="special_required" value="on" <?php (isset($password_data['special_required']) && $password_data['special_required'] == 1) ? print 'checked="checked"' : print '' ?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Special character required');?></label>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="StorePasswordSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
    </div>

</form>