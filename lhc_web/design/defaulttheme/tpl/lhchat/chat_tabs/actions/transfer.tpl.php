<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_transfer_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat', 'allowtransfer')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/transferchat/<?php echo $chat->id?>'})"><span class="material-icons">supervisor_account</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?></a>
</div>
<?php endif; ?>