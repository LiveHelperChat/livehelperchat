<?php if ( isset($orderInformation['voice_call']['enabled']) && $orderInformation['voice_call']['enabled'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhvoicevideo','use') ) : ?>
<?php $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data; if (isset($voiceData['voice']) && $voiceData['voice'] == true) : ?>
<tr>
    <td colspan="2" >
        <h6 class="font-weight-bold"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Voice & Video & ScreenShare')?></h6>

        <a onclick="window.open('<?php echo erLhcoreClassDesign::baseurl('voicevideo/joinoperator')?>/<?php echo $chat->id?>','lhc_voice_call','scrollbars=yes,menubar=1,resizable=1,width=800,height=600')" class="text-muted"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Start a call')?></a>

        <div id="call-status-<?php echo $chat->id?>">
            <?php if (($voiceCallInstance = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id,false)) instanceof erLhcoreClassModelChatVoiceVideo && $voiceCallInstance->id !== null) : ?>
                    <?php if ($voiceCallInstance->status == erLhcoreClassModelChatVoiceVideo::STATUS_PENDING) : ?>
                    <span class="badge badge-secondary fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Has not started')?></span>
                    <?php elseif ($voiceCallInstance->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRM) : ?>
                    <span class="badge badge-warning fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Visitor - waiting for permission to join the call')?></span>
                    <?php elseif ($voiceCallInstance->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED) : ?>
                    <span class="badge badge-success fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Visitor - permission granted')?></span>
                    <?php endif; ?>

                    <?php if ($voiceCallInstance->op_status == erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING) : ?>
                    <span class="badge badge-warning fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Pending operator to join the call')?></span>
                    <?php elseif ($voiceCallInstance->op_status == erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED) : ?>
                    <span class="badge badge-success fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Operator has joined the call')?></span>
                    <?php endif; ?>

                    <?php if ($voiceCallInstance->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING) : ?>
                    <span class="badge badge-warning fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Pending visitor to join the call')?></span>
                    <?php elseif ($voiceCallInstance->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_REQUESTED) : ?>
                    <span class="badge badge-warning fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Visitor requested to join a call')?></span>
                    <?php elseif ($voiceCallInstance->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED) : ?>
                    <span class="badge badge-success fs12 mr-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Visitor has joined a call')?></span>
                    <?php endif; ?>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endif;endif; ?>