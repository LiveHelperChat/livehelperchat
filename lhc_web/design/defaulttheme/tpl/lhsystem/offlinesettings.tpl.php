<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/offlinesettings','Offline settings');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

    <div class="form-group">
        <label><input type="checkbox" name="doNotsaveOffline" <?php if (isset($settings['do_not_save_offline']) && $settings['do_not_save_offline'] == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Do not save offline chats');?></label>
        <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("system/offlinesettings","Offline chat request won't be saved.");?></i></small></p>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="closeOffline" <?php if (isset($settings['close_offline']) && $settings['close_offline'] == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Automatically change offline chat status to closed');?></label>
        <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("system/offlinesettings","Then offline chat is registered it's status will be changed to closed chat.");?></i></small></p>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="save_as_email" <?php if (isset($settings['save_as_email']) && $settings['save_as_email'] == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Save as e-mail ticket if department mailbox is setup');?></label>
        <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("system/offlinesettings","Instead of saving as chat we will save it as e-mail ticket. We will create a ticket in the send folder which afterwards will be imported as e-mail ticket.");?></i></small></p>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-secondary" name="saveSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update'); ?>" />

</form>