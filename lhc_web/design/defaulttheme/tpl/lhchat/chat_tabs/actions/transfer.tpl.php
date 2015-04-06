<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_transfer_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat', 'allowtransfer')) : ?>
<a class="icon-users" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/transferchat/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
<?php endif; ?>