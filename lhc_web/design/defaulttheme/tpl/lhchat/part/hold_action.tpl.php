<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','holduse')) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hold/Un-Hold chat')?>" href="#" class="w-100 btn btn-outline-secondary <?php ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && !isset($chat->chat_variables_array['lhc_hldu'])) ? print ' btn-outline-info' : ''?>" id="hold-action-<?php echo $chat->id?>" onclick="return lhinst.holdAction('<?php echo $chat->id?>',$(this))">
    <i class="material-icons me-0">pan_tool</i>
</a>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Wait for visitor message and stop auto responder')?>" href="#" class="w-100 btn btn-outline-secondary <?php ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && isset($chat->chat_variables_array['lhc_hldu']) && $chat->chat_variables_array['lhc_hldu'] == true) ? print ' btn-outline-info' : ''?>" data-type="usr" id="hold-action-usr-<?php echo $chat->id?>" onclick="return lhinst.holdAction('<?php echo $chat->id?>',$(this))">
    <i class="material-icons me-0">hourglass_empty</i>
</a>
<?php endif; ?>