<?php include(erLhcoreClassDesign::designtpl('lhchat/part/speech_action_pre.tpl.php')); ?>
<?php if ($speech_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhspeech','use')) : ?>
			     <a class="btn btn-default icon-mic" href="#" id="mic-chat-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.speak','listen',{'chat_id':'<?php echo $chat->id?>'})"></a>
<?php endif;?>