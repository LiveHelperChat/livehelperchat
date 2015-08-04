<?php include(erLhcoreClassDesign::designtpl('lhchat/part/speech_action_pre.tpl.php')); ?>
<?php if ($speech_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhspeech','use')) : ?>
			     <a class="btn btn-default" href="#" id="mic-chat-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.speak','listen',{'chat_id':'<?php echo $chat->id?>'})"><i class="material-icons">mic_none</i><span class="mic-lang"></span></a>
<?php endif;?>