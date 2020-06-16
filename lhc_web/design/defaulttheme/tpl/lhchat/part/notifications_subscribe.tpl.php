<?php $notificationsSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings')->data_value;?>

<?php if (isset($notificationsSettings['enabled']) && $notificationsSettings['enabled'] == 1 && (!isset($theme) || $theme === false || (isset($theme->notification_configuration_array['notification_enabled']) && $theme->notification_configuration_array['notification_enabled'] == 1))) : ?>
    <i role="button" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Subscribe/Unsubscribe for notifications')?>" onclick="notificationsLHC.sendNotification()" class="material-icons"><?php if (isset($react) && $react == true) : ?>&#xf118;<?php else : ?>notifications<?php endif; ?></i>
<?php endif; ?>