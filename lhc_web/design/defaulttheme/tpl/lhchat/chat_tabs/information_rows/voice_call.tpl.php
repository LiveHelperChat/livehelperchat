<?php if ( isset($orderInformation['voice_call']['enabled']) && $orderInformation['voice_call']['enabled'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','take_screenshot') ) : ?>
<?php $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data; if (isset($voiceData['voice']) && $voiceData['voice'] == true) : ?>
<tr>
    <td colspan="2" >
        <h6 class="font-weight-bold"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Voice & Video & ScreenShare')?></h6>


        <a onclick="window.open('<?php echo erLhcoreClassDesign::baseurl('voicevideo/joinoperator')?>/<?php echo $chat->id?>','lhc_voice_call','scrollbars=yes,menubar=1,resizable=1,width=800,height=600')" class="text-muted"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Start a call')?></a>

        <div id="call-status-<?php echo $chat->id?>">
            <?php if (($voiceCallInstance = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id,false)) instanceof erLhcoreClassModelChatVoiceVideo) : ?>

            <ul>
                <li>
                    <?php if ($voiceCallInstance->status == erLhcoreClassModelChatVoiceVideo::STATUS_PENDING) : ?>
                    Pending
                    <?php elseif ($voiceCallInstance->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRM) : ?>
                    Visitor pending for confirm to join a chat
                    <?php elseif ($voiceCallInstance->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED) : ?>
                    Visitor has been confirmed to join a chat
                    <?php endif; ?>
                </li>
                <li>
                    <?php if ($voiceCallInstance->op_status == erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING) : ?>
                    Pending operator to join a call
                    <?php elseif ($voiceCallInstance->op_status == erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED) : ?>
                    Operator has joined a call
                    <?php endif; ?>
                </li>
                <li>
                    <?php if ($voiceCallInstance->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING) : ?>
                    Pending visitor to join a call
                    <?php elseif ($voiceCallInstance->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_REQUESTED) : ?>
                    Visitor requested to join a call
                    <?php elseif ($voiceCallInstance->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED) : ?>
                    Visitor has joined a call
                    <?php endif; ?>
                </li>
            </ul>



            <?php else : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Call have not been initialised')?>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endif;endif; ?>