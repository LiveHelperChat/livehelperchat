<div class="user-chatwidget-buttons" id="ChatSendButtonContainer">

    <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>

    <?php if (isset($fileData['sound_messages']) && $fileData['sound_messages'] == true) : ?>
    <div id="voice-control-message" style="display: none" class="text-nowrap">
        <i class="leave-recording-ui material-icons action-image text-danger mr-0 fs25" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Cancel')?>">close</i><i class="voice-start-recording material-icons fs25 action-image text-danger mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Record voice message')?>">fiber_manual_record</i><i style="display: none" class="spinner-pulsating voice-stop-recording material-icons fs25 action-image text-danger mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Stop recording')?>">stop</i><i style="display: none" class="voice-play-recording material-icons action-image text-success mr-0 fs25" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Play recorded audio')?>">play_arrow</i><i style="display: none" class="voice-stop-play material-icons action-image text-success mr-0 fs25" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Stop playing')?>">stop</i><span class="voice-audio-status mr-0 fs12">0s.</span><span style="display: none;" class="ml-1 voice-send-message" ><i class="material-icons text-success mr-0 action-image fs25" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Send voice message')?>">send</i></span>
    </div>
    <a id="lhc-mic-icon" onclick="return lhc.methodCall('lhc.voicevisitor','listen',{'chat_id': <?php echo $chat->id?>, 'hash': '<?php echo $chat->hash?>','length': <?php echo $fileData['sound_length']?>})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Record voice message')?>">
        <i class="material-icons text-muted settings mr-0">mic_none</i>
    </a>
    <?php endif; ?>

    <a href="#" <?php if (isset($fileData['sound_messages']) && $fileData['sound_messages'] == true) : ?>style="display: none"<?php endif;?> id="lhc-send-icon" onclick="<?php if (isset($chatbox)) : ?>lhinst.addmsguserchatbox();<?php else : ?>lhinst.addmsguser(true)<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat', 'Send') ?>">
        <i class="material-icons text-muted settings mr-0">send</i>
    </a>
</div>

