<?php include(erLhcoreClassDesign::designtpl('lhchat/part/voice_action_pre.tpl.php')); ?>

<?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data; ?>

<?php if (isset($fileData['sound_messages_op']) && $fileData['sound_messages_op'] == true && $voice_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','voicemessages')) : ?>
    <a class="w-100 btn btn-outline-secondary text-nowrap" title="Record voice message" href="#" id="voice-chat-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.voice','listen',{'chat_id':'<?php echo $chat->id?>'})"><i class="go-to-voice material-icons mr-0">record_voice_over</i><span class="voice-ui"></span></a>
<?php endif;?>