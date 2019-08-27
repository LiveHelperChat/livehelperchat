<?php include(erLhcoreClassDesign::designtpl('lhchat/part/speech_action_pre.tpl.php')); ?>
<?php if ($speech_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhspeech','use')) : ?>
			     <a class="w-100 btn btn-outline-secondary text-nowrap" href="#" id="mic-chat-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.speak','listen',{'chat_id':'<?php echo $chat->id?>'})"><i class="material-icons mr-0">mic_none</i><span class="mic-lang"></span></a>
<?php endif;?>