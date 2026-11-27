<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_blockuser_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowblockusers')) : ?>
<div class="col-6 pb-1">
<a class="text-muted" id="block-status-wrap-<?php echo $chat->id?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/blockuser/<?php echo $chat->id?>'})" ><span class="material-icons">block</span><span id="block-status-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block visitor')?></span></a>
</div>
<?php endif;?>