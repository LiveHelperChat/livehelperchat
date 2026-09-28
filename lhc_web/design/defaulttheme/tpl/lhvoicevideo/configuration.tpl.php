<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Voice & Video & ScreenShare'); ?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($voice_data['voice']) && $voice_data['voice'] == true && !erLhcoreClassVoiceVideo::isConfigured()) : ?>
    <div class="alert alert-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Calls are enabled, but media server is not configured. Calls are not available until LiveKit server URL, API key and API secret are set.'); ?></div>
<?php endif; ?>

<form action="" method="post" ng-non-bindable autocomplete="off">

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Features'); ?></h5>

    <label class="d-block"><input type="checkbox" name="voice" value="on" <?php isset($voice_data['voice']) && ($voice_data['voice'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Calls enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="video" value="on" <?php isset($voice_data['video']) && ($voice_data['video'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Video enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="screenshare" value="on" <?php isset($voice_data['screenshare']) && ($voice_data['screenshare'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','ScreenShare enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="log_calls" value="on" <?php (!isset($voice_data['log_calls']) || $voice_data['log_calls'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Add a message to the chat when a call ends (type and duration)'); ?></label>

    <hr>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Media server'); ?></h5>

    <div>
        <p class="text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Calls run on your own self hosted LiveKit server - open source (Apache 2.0) WebRTC media server. Audio, video and screen share never leave your infrastructure. See doc/voice_video/livekit for installation.'); ?></p>
        <div class="row form-group">
            <div class="col-md-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','LiveKit server URL'); ?></label>
                <input type="text" class="form-control form-control-sm" placeholder="wss://livekit.example.com" name="livekit_url" value="<?php isset($voice_data['livekit_url']) ? print htmlspecialchars($voice_data['livekit_url']) : '' ?>" />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','API key'); ?></label>
                <input type="text" class="form-control form-control-sm" name="livekit_api_key" value="<?php isset($voice_data['livekit_api_key']) ? print htmlspecialchars($voice_data['livekit_api_key']) : '' ?>" />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','API secret'); ?></label>
                <input type="password" class="form-control form-control-sm" name="livekit_api_secret" value="" autocomplete="new-password" placeholder="<?php echo (isset($voice_data['livekit_api_secret']) && $voice_data['livekit_api_secret'] != '') ? erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Stored. Leave empty to keep current value.') : ''; ?>" />
                <?php if (isset($voice_data['livekit_api_secret']) && $voice_data['livekit_api_secret'] != '') : ?>
                <label class="fs13"><input type="checkbox" name="livekit_api_secret_clear" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Remove stored secret'); ?></label>
                <?php endif; ?>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Webhook URL. Add it to livekit.yaml webhook section, so calls are ended correctly if a participant closes the browser or loses connection.'); ?></label>
                <input type="text" readonly class="form-control form-control-sm" value="<?php echo htmlspecialchars(erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('voicevideo/webhook'))?>" />
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Ring timeout in seconds. If nobody lets visitor in within this time, call request is marked as not answered. 0 - wait forever'); ?></label>
            <input type="number" min="0" max="3600" class="form-control form-control-sm" name="ring_timeout" value="<?php isset($voice_data['ring_timeout']) ? print (int)$voice_data['ring_timeout'] : print 60 ?>" />
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Access token lifetime in seconds. 0 - default (600)'); ?></label>
            <input type="number" min="0" max="86400" class="form-control form-control-sm" name="token_ttl" value="<?php isset($voice_data['token_ttl']) ? print (int)$voice_data['token_ttl'] : print 0 ?>" />
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-secondary" name="StoreVoiceConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
