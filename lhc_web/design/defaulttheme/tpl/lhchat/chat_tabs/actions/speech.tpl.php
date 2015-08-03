<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/speech_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_speech_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhspeech','change_chat_recognition')) : ?>
<a class="material-icons mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Choose other than default recognition language')?>" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('speech/setchatspeechlanguage')?>/<?php echo $chat->id?>'})">mic_none</a>
<?php endif;?>