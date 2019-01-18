<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/recaptcha','Re-captcha settings');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/recaptcha','This works with V3 re-captcha.');?> <a href="https://www.google.com/recaptcha/admin#v3signup" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/recaptcha','Get Re-captcha keys');?></a></p>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="enabled" <?php (isset($rc_data['enabled']) && $rc_data['enabled'] == 1) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Enable');?></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/recaptcha','Site key');?></label>
        <input type="text" class="form-control" name="site_key" value="<?php isset($rc_data['site_key']) ? print htmlspecialchars($rc_data['site_key']) : ''?>" />
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/recaptcha','Secret key');?></label>
        <input type="text" class="form-control" name="secret_key" value="" />
        <p><i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/recaptcha','Secret key is not shown!');?></small></i></p>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-secondary" name="StoreRecaptchaSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update'); ?>" />

</form>