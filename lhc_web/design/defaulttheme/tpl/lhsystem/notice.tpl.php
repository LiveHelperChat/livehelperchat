<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Notice messages')?></h1>
<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('system/notice')?>" method="post">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Generic notice message at the top bar')?></h5>

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
        <textarea rows="2" class="form-control form-control-sm" name="message"><?php echo isset($data['message']) ? htmlspecialchars($data['message']) : '' ?></textarea>
    </div>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Notice message in case of connection issues')?></h5>

    <div class="form-group">
        <label class="pb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Message')?> <button type="button" class="btn btn-secondary btn-xs" onclick="document.querySelector('textarea[name=message_connection]').value = '<div class=\'pt-1 text-danger fw-bold fs14\'><span class=\'material-icons\'>wifi_off</span>Connection problem detected. Please check your connectivity.</div>';">Set demo</button></label>
        <textarea rows="10" class="form-control form-control-sm" name="message_connection"><?php echo isset($data['message_connection']) ? htmlspecialchars($data['message_connection']) : '' ?></textarea>
    </div>

    <input type="submit" class="btn btn-secondary btn-sm" name="StoreUserSettingsAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
</form>

