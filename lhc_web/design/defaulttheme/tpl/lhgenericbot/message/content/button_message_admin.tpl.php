<div>
    <div><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video','Visitor requested to start a voice call.')?></div>
    <a onclick="window.open('<?php echo erLhcoreClassDesign::baseurl('voicevideo/joinoperator')?>/<?php echo $chat->id?>','lhc_voice_call','scrollbars=yes,menubar=1,resizable=1,width=800,height=600')" class="btn btn-sm btn-link"><i class="material-icons">call</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Start a call')?></a>
</div>