<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Notifications settings')?></h1>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','For more information see')?> <a target="_blank" href="https://developers.google.com/web/fundamentals/push-notifications/display-a-notification">https://developers.google.com/web/fundamentals/push-notifications/display-a-notification</a></p>

    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','You can generate your private and public key here')?> - <a target="_blank" href="https://web-push-codelab.glitch.me">https://web-push-codelab.glitch.me</a></p>

    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Explains were taken from:')?> <a target="_blank" href="https://web-push-book.gauntface.com/demos/notification-examples/">https://web-push-book.gauntface.com/demos/notification-examples/</a></p>

    <p><a href="<?php echo erLhcoreClassDesign::baseurl('notifications/downloadworker')?>"><i class="material-icons">cloud_download</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Download Service Worker')?></a> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','you will have to put this file in your domain root folder.')?></p>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="enabled" <?php isset($n_settings['enabled']) && ($n_settings['enabled'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Enable notifications')?></label><br/>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="require_interaction" <?php isset($n_settings['require_interaction']) && ($n_settings['require_interaction'] == true) ? print 'checked="checked"' : ''?> /> Require Interaction</label><br/>
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','On desktop, a notification is only displayed for a short period of time. On Android, notifications are shown until the user interacts with it.')?></p>

        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','To get the same behaviour on desktop and mobile you can set the "require-interaction" option to true, which means the user must click or dismiss the notification.')?></p>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="renotify" <?php isset($n_settings['renotify']) && ($n_settings['renotify'] == true) ? print 'checked="checked"' : ''?> /> Renotify</label><br/>
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','When you use the tag option, the default behavior of a new notification replacing an existing one is that there is no sound, vibration and the screen is kept asleep.')?></p>

        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','With `renotify: true` a new notification will play a sound, vibrate and wake up the users device. This means replacing notifications have the same behavior as a completely new notification.')?></p>

        <p><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Note')?>:</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','There is no visible affect on desktop, but on mobile, vibration and sound will be affected.')?></p>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Subject*')?></label>
        <input type="text" class="form-control" name="subject" value="<?php (isset($n_settings['subject'])) ? print htmlspecialchars($n_settings['subject']) : ''?>">
        <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','You must provide a subject that is either a mailto: or a URL.')?></i></small>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','HTTP Host*')?></label>
        <input type="text" class="form-control" name="http_host" value="<?php (isset($n_settings['http_host']) && !empty($n_settings['http_host'])) ? print htmlspecialchars($n_settings['http_host']) : print $_SERVER['HTTP_HOST']?>">
        <small><i>You must provide host for notifications images.</i></small>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Default Icon')?></label>
        <input type="text" class="form-control" name="icon" value="<?php if (isset($n_settings['icon'])) : ?><?php print htmlspecialchars($n_settings['icon']); else : ?>https://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/logo.png');?><?php endif;?>">
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Badge Icon')?></label>
        <input type="text" class="form-control" name="badge" value="<?php if (isset($n_settings['badge'])) : ?><?php print htmlspecialchars($n_settings['badge']); else : ?>https://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/logo.png');?><?php endif;?>">
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Notification badges are only being used on mobile, at least at the time of writing. It is used to replace the browser icon that is shown by default.')?></p>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Vibrate')?></label>
        <input type="text" class="form-control" name="vibrate" value="<?php if (isset($n_settings['vibrate'])) : ?><?php print htmlspecialchars($n_settings['vibrate']); endif ?>">
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Public key*')?></label>
        <input type="text" class="form-control" name="public_key" value="<?php (isset($n_settings['public_key'])) ? print htmlspecialchars($n_settings['public_key']) : ''?>">
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Private key*')?></label>
        <input type="text" class="form-control" name="private_key" value="">
        <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/notifications','Private key is not shown after save')?></i></small>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
