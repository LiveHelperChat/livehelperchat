<h1>Voice & Video & ScreenShare</h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <h5>Features</h5>
    <label class="d-block"><input type="checkbox" name="voice" value="on" <?php isset($voice_data['voice']) && ($voice_data['voice'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Voice calls enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="video" value="on" <?php isset($voice_data['video']) && ($voice_data['video'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Video calls enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="screenshare" value="on" <?php isset($voice_data['screenshare']) && ($voice_data['screenshare'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ScreenShare enabled'); ?></label>

    <h5>Agora integration</h5>
    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Agora APP ID'); ?></label>
            <input type="text" class="form-control" name="agora_app_id" value="<?php isset($voice_data['agora_app_id']) ? print $voice_data['agora_app_id'] : '' ?>" />
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Agora App Certificate'); ?></label>
            <input type="text" class="form-control" name="agora_app_token" value="<?php isset($voice_data['agora_app_token']) ? print $voice_data['agora_app_token'] : '' ?>" />
        </div>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreVoiceConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>


