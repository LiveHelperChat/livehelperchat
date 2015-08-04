<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_transfer_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat', 'allowtransfer')) : ?>
<a class="material-icons mr-0" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/transferchat/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">supervisor_account</a>
<?php endif; ?>