<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action_pre.tpl.php')); ?>
<?php if ($chat_part_canned_messages_action_enabled == true) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" class="btn btn-secondary w-100 btn-sm send-delayed-canned" onclick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))">
    <i class="material-icons mr-0">mail</i>
</a>
<?php endif;?>