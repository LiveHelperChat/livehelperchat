<?php if ($chat->user_id == erLhcoreClassUser::instance()->getUserID() || erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowcloseremote')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" id="chat-close-action-<?php echo $chat->id?>" data-status="<?php echo $chat->status;?>" data-loading="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closing...')?>" onclick="lhinst.closeActiveChatDialog(<?php echo $chat->id?>,$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>">
        <span class="material-icons">close</span><span data-original="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>" class="close-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?></span></a>
</div>
<?php endif;?>