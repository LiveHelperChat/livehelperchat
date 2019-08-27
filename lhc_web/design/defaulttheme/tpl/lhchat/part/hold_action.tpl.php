<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','holduse')) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hold/Un-Hold chat')?>" href="#" class="w-100 btn btn-outline-secondary <?php ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD) ? print ' btn-info' : ''?>" id="hold-action-<?php echo $chat->id?>" onclick="return lhinst.holdAction('<?php echo $chat->id?>',$(this))">
    <i class="material-icons mr-0">pan_tool</i>
</a>
<?php endif; ?>