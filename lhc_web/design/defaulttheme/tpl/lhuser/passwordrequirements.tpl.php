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
        <input type="number" class="form-control form-control-sm" name="expires_in" value="<?php (isset($password_data['expires_in'])) ? print htmlspecialchars($password_data['expires_in']) : print '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Disable user automatically if from last login passed X number of days');?></label>
        <input type="number" class="form-control form-control-sm" name="disable_after" value="<?php (isset($password_data['disable_after'])) ? print htmlspecialchars($password_data['disable_after']) : print '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Force user logout if last login was X hours ago');?></label>
        <input type="number" class="form-control form-control-sm" name="logout_after" value="<?php (isset($password_data['logout_after'])) ? print htmlspecialchars($password_data['logout_after']) : print '' ?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Maximum number of failed logins before account is disabled.');?></label>
        <input type="number" class="form-control form-control-sm" name="max_attempts" value="<?php (isset($password_data['max_attempts'])) ? print htmlspecialchars($password_data['max_attempts']) : print '' ?>" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" class="me-1" value="on" <?php (isset($password_data['generate_manually']) && $password_data['generate_manually'] == 1) ? print 'checked="checked"': print '' ?> name="generate_manually" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','We should generate password for password reminder');?></label>
    </div>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Login restrictions');?></h5>

    <ul>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('2fa/admin','Wildcard format')?>:     1.2.3.*</li>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('2fa/admin','CIDR format')?>:         1.2.3/24  OR  1.2.3.4/255.255.255.0</li>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('2fa/admin','Start-End IP format')?>: 1.2.3.0-1.2.3.255</li>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('2fa/admin','Your IP')?>: <span class="badge bg-secondary"><?php echo htmlspecialchars(erLhcoreClassIPDetect::getIP());?></span></li>
    </ul>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Allow to login only from these IP');?></label>
        <input placeholder="191.168.1.*" name="allow_login_from_ip" type="text" class="form-control form-control-sm" value="<?php echo isset($password_data['allow_login_from_ip']) ? htmlspecialchars($password_data['allow_login_from_ip']) : '';?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("users/autologin","To these users id's login IP restrictions does not apply");?></label>
        <input placeholder="1,2" name="bypass_ip_user_id" type="text" class="form-control form-control-sm" value="<?php echo isset($password_data['bypass_ip_user_id']) ? htmlspecialchars($password_data['bypass_ip_user_id']) : '';?>" />
    </div>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Password format requirements');?></h5>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Minimal password length');?></label>
        <input type="number" class="form-control form-control-sm" name="length" value="<?php (isset($password_data['length'])) ? print htmlspecialchars($password_data['length']) : print '' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Uppercase letters required');?></label>
        <input type="number" class="form-control form-control-sm" name="uppercase_required" value="<?php (isset($password_data['uppercase_required'])) ? print htmlspecialchars($password_data['uppercase_required']) : print '0' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Lowercase characters required');?></label>
        <input type="number" class="form-control form-control-sm" name="lowercase_required" value="<?php (isset($password_data['lowercase_required'])) ? print htmlspecialchars($password_data['lowercase_required']) : print '0' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Numbers required');?></label>
        <input type="number" class="form-control form-control-sm" name="number_required" value="<?php (isset($password_data['number_required'])) ? print htmlspecialchars($password_data['number_required']) : print '0' ?>" />
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Special characters required');?></label>
        <input type="number" class="form-control form-control-sm" name="special_required" value="<?php (isset($password_data['special_required'])) ? print htmlspecialchars($password_data['special_required']) : print '0' ?>" />
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="StorePasswordSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
    </div>


</form>