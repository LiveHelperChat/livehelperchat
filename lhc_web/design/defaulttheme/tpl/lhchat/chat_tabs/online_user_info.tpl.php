<?php if (($online_user = $chat->online_user) !== false) : ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_pre.tpl.php'));?>	

<?php if ($information_tab_online_user_info_enabled == true) : ?>
<div role="tabpanel" class="tab-pane<?php if ($chatTabsOrderDefault == 'online_user_info_tab') print ' active';?>" id="online-user-info-tab-<?php echo $chat->id?>">
	<a class="btn btn-default btn-xs" rel="<?php echo $chat->id?>" onclick="lhinst.refreshOnlineUserInfo($(this))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?></a>

	<div id="online-user-info-<?php echo $chat->id?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>
	</div>	
</div>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_chats_pre.tpl.php'));?>	

<?php if ($information_tab_online_user_info_chats_enabled == true) : ?>
<div role="tabpanel" class="tab-pane" id="online-user-info-chats-tab-<?php echo $chat->id?>">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_chats_list_override.tpl.php'));?>
</div>
<?php endif; ?>

<?php endif; ?>
