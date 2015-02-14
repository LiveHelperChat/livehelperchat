<?php if ( ($online_user = $chat->online_user) !== false) : ?>
<div role="tabpanel" class="tab-pane" id="online-user-info-tab-<?php echo $chat->id?>">
	<a class="btn btn-default btn-xs" rel="<?php echo $chat->id?>" onclick="lhinst.refreshOnlineUserInfo($(this))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?></a>

	<div id="online-user-info-<?php echo $chat->id?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>
	</div>	
</div>
<?php //include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats.tpl.php')); ?>
<?php endif; ?>


