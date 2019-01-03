<?php $startChatText = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>
<?php if (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['custom_start_button']) && !empty($theme->bot_configuration_array['custom_start_button']) && $onlyBotOnline == false) {
    $startChatText = htmlspecialchars($theme->bot_configuration_array['custom_start_button']);
} elseif (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['custom_start_button_bot']) && !empty($theme->bot_configuration_array['custom_start_button_bot']) && $onlyBotOnline == true) {
    $startChatText = htmlspecialchars($theme->bot_configuration_array['custom_start_button_bot']);
} ?>
<input type="submit" class="btn btn-secondary btn-sm startchat" value="<?php echo $startChatText?>" name="StartChatAction" />