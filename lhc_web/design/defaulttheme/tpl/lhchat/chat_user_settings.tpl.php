<?php
$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message', (int)($soundData['new_message_sound_user_enabled']));
?>
<div class="btn-group dropup topi">
    <i class="material-icons settings" data-toggle="dropdown" aria-haspopup="true"
       aria-expanded="false">settings</i>
    <a class="text-muted" href="#" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>'})" title="BB Code">
        <i class="material-icons smile">face</i>
    </a>

    <div class="dropdown-menu shadow bg-white rounded">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_sound.tpl.php')); ?>

        <?php if (isset($chat)) : ?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/notifications.tpl.php')); ?>

            <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_print.tpl.php')); ?>
            <?php endif; ?>

            <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_transcript.tpl.php')); ?>
            <?php endif; ?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/user_file_upload.tpl.php')); ?>

        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_last_multiinclude.tpl.php')); ?>
    </div>
</div>

