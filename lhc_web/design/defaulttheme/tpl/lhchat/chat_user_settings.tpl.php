<?php
$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message', (int)($soundData['new_message_sound_user_enabled']));
?>

<div class="d-flex flex-row">

        <div class="btn-group dropup p-1">
            <i class="material-icons settings text-muted" data-toggle="dropdown" aria-haspopup="true"
               aria-expanded="false">settings</i>

            <div class="dropdown-menu shadow bg-white rounded">
                <div class="d-flex flex-row">

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_sound.tpl.php')); ?>

                    <?php if (isset($chat)) : ?>

                        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/notifications.tpl.php')); ?>

                        <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>

                            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_print.tpl.php')); ?>

                        <?php endif; ?>


                        <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>

                            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_transcript.tpl.php')); ?>

                        <?php endif; ?>


                        <a class="text-muted" href="#"
                           onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert') ?>'})"
                           title="BB Code">
                            <i class="material-icons smile">face</i>
                        </a>


                        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/user_file_upload.tpl.php')); ?>


                        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_user_settings/option_last_multiinclude.tpl.php')); ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>

    <div class="mx-auto pb-1">
               <textarea autofocus="autofocus" class="form-control form-control-sm  live-chat-message pb-1" rows="1"
                         cols="120"
                         aria-required="true" required name="ChatMessage"
                         aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', trim('Type your message here...')); ?>"
                         placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat', trim('Type your message here...')); ?>"
                         id="CSChatMessage">
               </textarea>
    </div>
    <div>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_text_area_user.tpl.php')); ?>
    </div>
</div>


