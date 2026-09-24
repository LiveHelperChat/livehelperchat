<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Voice & Video & ScreenShare'); ?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $provider = isset($voice_data['provider']) && $voice_data['provider'] == 'livekit' ? 'livekit' : 'agora'; ?>

<form action="" method="post" ng-non-bindable autocomplete="off">

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Features'); ?></h5>

    <label class="d-block"><input type="checkbox" name="voice" value="on" <?php isset($voice_data['voice']) && ($voice_data['voice'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Calls enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="video" value="on" <?php isset($voice_data['video']) && ($voice_data['video'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Video enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="screenshare" value="on" <?php isset($voice_data['screenshare']) && ($voice_data['screenshare'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','ScreenShare enabled'); ?></label>
    <label class="d-block"><input type="checkbox" name="log_calls" value="on" <?php (!isset($voice_data['log_calls']) || $voice_data['log_calls'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Add a message to the chat when a call ends (type and duration)'); ?></label>

    <hr>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Media provider'); ?></h5>

    <div class="row form-group">
        <div class="col-md-6">
            <select name="provider" class="form-control form-control-sm" id="vvsh-provider" onchange="document.querySelectorAll('.vvsh-provider-options').forEach(function(el){el.classList.toggle('hide', el.getAttribute('data-provider') != document.getElementById('vvsh-provider').value)})">
                <option value="livekit" <?php $provider == 'livekit' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','LiveKit - open source, self hosted'); ?></option>
                <option value="agora" <?php $provider == 'agora' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Agora - cloud service'); ?></option>
            </select>
        </div>
    </div>

    <div class="vvsh-provider-options<?php $provider != 'livekit' ? print ' hide' : ''?>" data-provider="livekit">
        <p class="text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','LiveKit is an open source (Apache 2.0) WebRTC media server. Media never leaves your infrastructure. See doc/voice_video/livekit for a docker compose example.'); ?></p>
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
    </div>

    <div class="vvsh-provider-options<?php $provider != 'agora' ? print ' hide' : ''?>" data-provider="agora">
        <div class="row form-group">
            <div class="col-md-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Agora APP ID'); ?></label>
                <input type="text" class="form-control form-control-sm" name="agora_app_id" value="<?php isset($voice_data['agora_app_id']) ? print htmlspecialchars($voice_data['agora_app_id']) : '' ?>" />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Agora App Certificate'); ?></label>
                <input type="password" class="form-control form-control-sm" name="agora_app_token" value="" autocomplete="new-password" placeholder="<?php echo (isset($voice_data['agora_app_token']) && $voice_data['agora_app_token'] != '') ? erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Stored. Leave empty to keep current value.') : ''; ?>" />
                <?php if (isset($voice_data['agora_app_token']) && $voice_data['agora_app_token'] != '') : ?>
                <label class="fs13"><input type="checkbox" name="agora_app_token_clear" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Remove stored certificate'); ?></label>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('voice/configuration','Access token lifetime in seconds. 0 - default (LiveKit 600, Agora 300)'); ?></label>
            <input type="number" min="0" max="86400" class="form-control form-control-sm" name="token_ttl" value="<?php isset($voice_data['token_ttl']) ? print (int)$voice_data['token_ttl'] : print 0 ?>" />
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-secondary" name="StoreVoiceConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
