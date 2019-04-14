<?php $notificationsSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings')->data_value;?>

<?php if (isset($notificationsSettings['enabled']) && $notificationsSettings['enabled'] == 1 && (!isset($theme) || $theme === false || (isset($theme->notification_configuration_array['notification_enabled']) && $theme->notification_configuration_array['notification_enabled'] == 1))) : ?>
        <a role="button" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Subscribe/Unsubscribe for notifications')?>" onclick="notificationsLHC.sendNotification()"><i class="material-icons chat-setting-item text-muted">notifications</i></a>
<?php endif; ?>