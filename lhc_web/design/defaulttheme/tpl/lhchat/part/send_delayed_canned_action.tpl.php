<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action_pre.tpl.php')); ?>
<?php if ($chat_part_canned_messages_action_enabled == true) : ?>
<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" class="w-100 btn btn-outline-secondary" onclick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))">
    <i class="material-icons">mail</i>
</a>
<?php endif;?>