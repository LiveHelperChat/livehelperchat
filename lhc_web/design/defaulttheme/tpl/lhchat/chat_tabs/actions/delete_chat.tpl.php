<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deleteglobalchat') || (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deletechat') && $chat->user_id == erLhcoreClassUser::instance()->getUserID())) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)" ><span class="material-icons">delete</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?></a>
</div>
<?php endif ?>