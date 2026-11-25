<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Voice & Video & ScreenShare'); ?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" ng-non-bindable>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Features'); ?></h5>

    <label class="d-block"><input type="checkbox" name="voice" value="on" <?php isset($voice_data['voice']) && ($voice_data['voice'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Calls enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="video" value="on" <?php isset($voice_data['video']) && ($voice_data['video'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Video enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="screenshare" value="on" <?php isset($voice_data['screenshare']) && ($voice_data['screenshare'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','ScreenShare enabled'); ?></label>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Agora integration'); ?></h5>
    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Agora APP ID'); ?></label>
            <input type="text" class="form-control" name="agora_app_id" value="<?php isset($voice_data['agora_app_id']) ? print $voice_data['agora_app_id'] : '' ?>" />
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Agora App Certificate'); ?></label>
            <input type="text" class="form-control" name="agora_app_token" value="<?php isset($voice_data['agora_app_token']) ? print $voice_data['agora_app_token'] : '' ?>" />
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-secondary" name="StoreVoiceConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>


