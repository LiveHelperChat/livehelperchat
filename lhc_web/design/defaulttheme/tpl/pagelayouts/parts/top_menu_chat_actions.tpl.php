<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>
<?php if ($parts_top_menu_chat_actions_enabled == true) : ?>

<?php if ($currentUser->hasAccessTo('lhchat','allowchattabs')) : ?>
<li class="li-icon"><a href="javascript:void(0)" onclick="javascript:lhinst.chatTabsOpen()"><i class="icon-chat"></i></a></li>		
<?php endif;?>
	
<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats list');?></a></li>
<?php endif;?>