<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Notice message')?></h1>
<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('system/notice')?>" method="post">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Level of notice')?></label>
        <select name="level" class="form-control form-control-sm">
            <option value="primary" <?php if (isset($data['level']) && $data['level'] == 'primary') : ?>selected="selected"<?php endif; ?> >Primary (blue)</option>
            <option value="warning" <?php if (isset($data['level']) && $data['level'] == 'warning') : ?>selected="selected"<?php endif; ?>>Warning (yellow)</option>
            <option value="danger" <?php if (isset($data['level']) && $data['level'] == 'danger') : ?>selected="selected"<?php endif; ?>>Danger (red)</option>
            <option value="success" <?php if (isset($data['level']) && $data['level'] == 'success') : ?>selected="selected"<?php endif; ?>>Success (green)</option>
        </select>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Message')?></label>
        <textarea class="form-control form-control-sm" name="message"><?php echo isset($data['message']) ? htmlspecialchars($data['message']) : '' ?></textarea>
    </div>

    <input type="submit" class="btn btn-secondary btn-sm" name="StoreUserSettingsAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
</form>

