<?php $notificationsSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings')->data_value;?>

<?php if (isset($notificationsSettings['enabled']) && $notificationsSettings['enabled'] == 1 && (!isset($theme) || $theme === false || (isset($theme->notification_configuration_array['notification_enabled']) && $theme->notification_configuration_array['notification_enabled'] == 1))) : ?>
    <li role="menuitem">
        <a role="button" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Subscribe/Unsubscribe for notifications')?>" onclick="notificationsLHC.sendNotification()" class="material-icons mat-100 mr-0">notifications</a>
    </li>
<?php endif; ?>