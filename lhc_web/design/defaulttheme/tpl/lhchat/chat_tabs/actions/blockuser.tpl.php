<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_blockuser_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowblockusers')) : ?>
<div class="col-6 pb-1">
<a class="text-muted" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/blockuser/<?php echo $chat->id?>'})" ><span class="material-icons">block</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block visitor')?></a>
</div>
<?php endif;?>