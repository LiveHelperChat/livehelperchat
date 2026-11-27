<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/speech_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_speech_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhspeech','change_chat_recognition')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('speech/setchatspeechlanguage')?>/<?php echo $chat->id?>'})">
        <span class="material-icons">mic_none</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Choose recognition language')?>
    </a>
</div>
<?php endif;?>