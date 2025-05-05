<h1 class="attr-header">Mobile Options</h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="notifications" <?php isset($mb_options['notifications']) && ($mb_options['notifications'] == true) ? print 'checked="checked"' : ''?> /> Enable notifications</label><br/>
    </div>

    <div class="form-group">
        <label>Bearer token. Delete content to generate new one.</label>
        <input type="text" class="form-control" name="fcm_key" value="<?php isset($mb_options['fcm_key']) ? print htmlspecialchars($mb_options['fcm_key']) : ''?>" />
    </div>

    <h3>Override default lists limits</h3>
    <div class="form-group">
        <label>Pending chats limit</label>
        <input type="text" class="form-control" placeholder="10" name="limit_p" value="<?php isset($mb_options['limit_p']) ? print htmlspecialchars($mb_options['limit_p']) : ''?>" />
    </div>

    <div class="form-group">
        <label>Active chats limit</label>
        <input type="text" class="form-control" placeholder="10" name="limit_a" value="<?php isset($mb_options['limit_a']) ? print htmlspecialchars($mb_options['limit_a']) : ''?>" />
    </div>

    <div class="form-group">
        <label>Closed chats limit</label>
        <input type="text" class="form-control" placeholder="10" name="limit_c" value="<?php isset($mb_options['limit_c']) ? print htmlspecialchars($mb_options['limit_c']) : ''?>" />
    </div>

    <div class="form-group">
        <label>Bot chats limit</label>
        <input type="text" class="form-control" placeholder="10" name="limit_b" value="<?php isset($mb_options['limit_b']) ? print htmlspecialchars($mb_options['limit_b']) : ''?>" />
    </div>

    <div class="form-group">
        <label>Online visitors limit</label>
        <input type="text" class="form-control" placeholder="50" name="limit_ov" value="<?php isset($mb_options['limit_ov']) ? print htmlspecialchars($mb_options['limit_ov']) : ''?>" />
    </div>

    <div class="form-group">
        <label>Online operators limit</label>
        <input type="text" class="form-control" placeholder="50" name="limit_op" value="<?php isset($mb_options['limit_op']) ? print htmlspecialchars($mb_options['limit_op']) : ''?>" />
    </div>

    <h3>Custom mobile application integration service_account file.</h3>
    <div class="form-group">
        <label><input type="checkbox" value="on" name="use_local_service_file" <?php isset($mb_options['use_local_service_file']) && ($mb_options['use_local_service_file'] == true) ? print 'checked="checked"' : ''?> /> Use local service file</label><br/>
        <span class="badge bg-info">var/external/service_account.php</span> - <?php file_exists('var/external/service_account.php') ? print '<span class="badge bg-success">Found</span>' : print '<span class="badge bg-danger">NOT found</span>'; ?> It should have content like
<pre>
&lt;?php
return '{
    "type": "service_account",
    "project_id": "",
    "private_key_id": "",
    "private_key": "",
    "client_email": "",
    "client_id": "",
    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    "token_uri": "https://oauth2.googleapis.com/token",
    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
    "client_x509_cert_url": "",
    "universe_domain": "googleapis.com"
  }';
</pre>



    </div>



    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
