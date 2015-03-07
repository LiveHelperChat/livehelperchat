<?php if (($online_user = $chat->online_user) !== false) : ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_pre.tpl.php'));?>	

<?php if ($information_tab_online_user_info_enabled == true) : ?>
<div role="tabpanel" class="tab-pane" id="online-user-info-tab-<?php echo $chat->id?>">
	<a class="btn btn-default btn-xs" rel="<?php echo $chat->id?>" onclick="lhinst.refreshOnlineUserInfo($(this))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?></a>

	<div id="online-user-info-<?php echo $chat->id?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>
	</div>	
</div>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_chats_pre.tpl.php'));?>	

<?php if ($information_tab_online_user_info_chats_enabled == true) : ?>
<div role="tabpanel" class="tab-pane" id="online-user-info-chats-tab-<?php echo $chat->id?>">
  <ul class="foot-print-content list-unstyled" style="max-height: 170px;">
	<?php foreach (erLhcoreClassChat::getList(array('limit' => 100, 'filter' => array('online_user_id' => $online_user->id))) as $chatPrev) : ?>
		<?php if (!isset($chat) || $chat->id != $chatPrev->id) : ?>
			<li>
			  <?php if ( !empty($chatPrev->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chatPrev->country_code?>.png" alt="<?php echo htmlspecialchars($chatPrev->country_name)?>" title="<?php echo htmlspecialchars($chatPrev->country_name)?>" />&nbsp;<?php endif; ?>
		      <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="icon-popup" onclick="lhinst.startChatNewWindow('<?php echo $chatPrev->id;?>',$(this).attr('data-title'))" data-title="<?php echo htmlspecialchars($chatPrev->nick,ENT_QUOTES);?>"></a><?php echo $chatPrev->id;?>. <?php echo htmlspecialchars($chatPrev->nick);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chatPrev->time);?>) (<?php echo htmlspecialchars($chatPrev->department);?>)
			</li>
		<?php endif; ?>
	<?php endforeach;?>
	</ul>
</div>
<?php endif; ?>

<?php endif; ?>