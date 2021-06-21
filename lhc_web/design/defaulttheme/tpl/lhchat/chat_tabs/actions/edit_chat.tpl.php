<div class="col-6 pb-1">
<a class="text-muted" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Modify chat')?>', 'iframe':true,'height':350,'mparams':{'backdrop':false},'url':WWW_DIR_JAVASCRIPT +'chat/modifychat/<?php echo $chat->id?>/(pos)/'+$('#chat-tab-li-<?php echo $chat->id?>').index()})" >
    <i class="material-icons">mode_edit</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Edit chat')?>
</a>
</div>