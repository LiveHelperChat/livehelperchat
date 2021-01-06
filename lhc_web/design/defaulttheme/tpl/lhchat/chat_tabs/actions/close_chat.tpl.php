<?php if ($chat->user_id == erLhcoreClassUser::instance()->getUserID() || erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowcloseremote')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" onclick="lhinst.closeActiveChatDialog(<?php echo $chat->id?>,$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>"><span class="material-icons">close</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?></a>
</div>
<?php endif;?>