<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_blockuser_enabled == true) : ?>
<a class="material-icons mr-0" data-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?')?>" onclick="lhinst.blockUser('<?php echo $chat->id?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>">block</a>
<?php endif;?>