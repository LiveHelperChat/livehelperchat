<div class="msg-body">
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Operator has requested a voice call with you!')?>
</div>

<div class="meta-message meta-message-<?php echo $messageId?>">
    <div class="py-2">
        <button type="button" data-no-change="true" onclick="lhinst.startVoiceCall()" class="btn btn-sm btn-primary btn-bot"><i class="material-icons">&#xf117;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Start a call')?></button>
    </div>
</div>