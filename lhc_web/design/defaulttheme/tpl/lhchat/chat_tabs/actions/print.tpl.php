<?php if (isset($orderChatButtons['print']) && $orderChatButtons['print']['enabled'] == 1) : ?>
<div class="col-6 pb-1">
    <a target="_blank" class="text-muted" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchatadmin')?>/<?php echo $chat->id?>">
        <i class="material-icons">print</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Print')?>
    </a>
</div>
<?php endif; ?>