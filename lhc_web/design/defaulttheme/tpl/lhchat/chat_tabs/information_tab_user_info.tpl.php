<div class="pull-right operator-info pt5">
	<i class="icon-thumbs-up<?php if ($chat->fbst == 1) : ?> up-voted<?php endif;?>"></i>
	<i class="icon-thumbs-down<?php if ($chat->fbst == 2) : ?> down-voted<?php endif;?>"></i>

	<span class="pull-right label label-default fs11<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?> action-image<?php endif?>" id="chat-status-text-<?php echo $chat->id?>" <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Click to change chat status')?>" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/changestatus/<?php echo $chat->id?>'})"<?php endif;?>>
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>
			<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Pending chat')?>
		<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
			<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Active chat')?>
		<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
			<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closed chat')?>
		<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
			<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chatbox chat')?>
		<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
			<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Operators chat')?>
		<?php endif;?>
	</span>
		
</div>
	 
<div>

<a onclick="return lhc.revealModal({'iframe':true,'height':350,'url':WWW_DIR_JAVASCRIPT +'chat/modifychat/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Edit main chat information')?>" href="#"><i class="icon-pencil"></i></a>

<?php if (!isset($hideActionBlock)) : ?>
		
		<a class="icon-popup" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatCloseTabNewWindow('<?php echo $chat->id;?>',$('#tabs'),$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">
					
		<a class="icon-cancel" onclick="lhinst.removeDialogTab('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>"></a>
		
		<?php if ($chat->user_id == erLhcoreClassUser::instance()->getUserID() || erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowcloseremote')) : ?>
		<a class="icon-cancel-circled" onclick="lhinst.closeActiveChatDialog('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>"></a>
		<?php endif;?>
		
		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deleteglobalchat') || (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deletechat') && $chat->user_id == erLhcoreClassUser::instance()->getUserID())) : ?>
		<a class="icon-cancel-squared" onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>"></a>
		<?php endif ?>	
		
		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat', 'allowtransfer')) : ?>
		<a class="icon-users" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/transferchat/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
		<?php endif; ?>

		<a class="icon-block" data-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?')?>" onclick="lhinst.blockUser('<?php echo $chat->id?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>"></a>
			
		<a class="icon-mail <?php if ($chat->mail_send == 1) : ?>icon-mail-send<?php endif; ?>" onclick="lhc.revealModal({'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'chat/sendmail/<?php echo $chat->id?>'})" title="<?php if ($chat->mail_send == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mail was send')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?><?php endif;?>"></a>
		
		<a class="icon-reply" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect user to contact form.');?>" onclick="lhinst.redirectContact('<?php echo $chat->id;?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?');?>')" ></a>
			
		<a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchatadmin')?>/<?php echo $chat->id?>" class="icon-print" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Print')?>"></a>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/attatch_file.tpl.php'));?>
		
		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowredirect')) : ?>
		<a class="icon-network" onclick="lhinst.redirectToURL('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Please enter a URL');?>')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect user to another url');?>"></a>
		<?php endif;?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/cobrowse.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_actions_extension_multiinclude.tpl.php'));?>
		 	
	<?php else : ?>
	<a class="icon-print" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/printchatadmin')?>/<?php echo $archive->id?>/<?php echo $chat->id?>"></a>
	<a class="icon-mail" onclick="return lhc.revealModal({'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'chatarchive/sendmail/<?php echo $archive->id?>/<?php echo $chat->id?>'})"></a>
	<?php endif; ?>	
</div>


<table class="table table-condensed">
	<?php if ( $chat->department !== false ) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Department')?></td>
		<td><?php echo htmlspecialchars($chat->department);?></td>
	</tr>
	<?php endif;?>
	
	<?php if ( !empty($chat->country_code) ) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Country')?></td>
		<td><img src="<?php echo erLhcoreClassDesign::design('images/flags')?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" /></td>
	</tr>
	<?php endif;?>
	
	<?php if ( !empty($chat->user_tz_identifier) ) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Time zone')?></td>
		<td><?php echo htmlspecialchars($chat->user_tz_identifier)?>, <?php echo htmlspecialchars($chat->user_tz_identifier_time)?></td>
	</tr>
	<?php endif;?>
	
	
	<?php if ( !empty($chat->city) ) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','City')?></td>
		<td><?php echo htmlspecialchars($chat->city);?></td>
	</tr>
	<?php endif;?>
	<tr>
		<td>IP</td>
		<td><?php echo $chat->ip?></td>
	</tr>
	<?php if (!empty($chat->referrer)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Page')?></td>
		<td><div class="page-url"><span><?php echo $chat->referrer != '' ? '<a target="_blank" title="' . htmlspecialchars($chat->referrer) . '" href="' .htmlspecialchars($chat->referrer). '">'.htmlspecialchars($chat->referrer).'</a>' : ''?></span></div></td>
	</tr>
	<?php endif;?>

	<?php if (!empty($chat->session_referrer)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?></td>
		<td><div class="page-url"><span><?php echo $chat->session_referrer != '' ? '<a target="_blank" title="' . htmlspecialchars($chat->session_referrer) . '" href="' . htmlspecialchars($chat->session_referrer) . '">'.htmlspecialchars($chat->session_referrer).'</a>' : ''?></span></div></td>
	</tr>
	<?php endif;?>
	<tr>
		<td>ID</td>
		<td><?php echo $chat->id;?></td>
	</tr>
	<?php if (!empty($chat->email)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?></td>
		<td><a href="mailto:<?php echo $chat->email?>"><?php echo $chat->email?></a></td>
	</tr>
	<?php endif;?>

	<?php if (!empty($chat->phone)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Phone')?></td>
		<td><?php echo htmlspecialchars($chat->phone)?></td>
	</tr>
	<?php endif;?>
	<?php if (!empty($chat->additional_data)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Additional data')?></td>
		<td>		
		<?php if (is_array($chat->additional_data_array)) : ?>
			<ul class="circle mb0">
				<?php foreach ($chat->additional_data_array as $addItem) : ?>
				<li><?php echo htmlspecialchars($addItem->key)?> - <?php echo htmlspecialchars($addItem->value)?></li>
				<?php endforeach;?>
			</ul>
		<?php else : ?>
		<?php echo htmlspecialchars($chat->additional_data)?>
		<?php endif;?>
		</td>
	</tr>
	<?php endif;?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created')?></td>
		<td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?></td>
	</tr>

	<?php if ($chat->user_closed_ts > 0 && $chat->user_status == 1) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','User left')?></td>
		<td><?php echo $chat->user_closed_ts_front?></td>
	</tr>
	<?php endif;?>
	
	<?php if ($chat->wait_time > 0) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?></td>
		<td><?php echo $chat->wait_time_front?> </td>
	</tr>
	<?php endif;?>

	<?php if ($chat->chat_duration > 0) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?></td>
		<td><?php echo $chat->chat_duration_front?></td>
	</tr>
	<?php endif;?>
	
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?></td>
		<td><?php echo $chat->chat_duration_front?></td>
	</tr>
		
    <?php if ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
    <tr>
	   <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat between operators, chat initializer')?></td>    	
	   <td><?php echo htmlspecialchars($chat->nick)?></td>
	</tr>
	<?php endif;?>	
    <tr>
    	<td>
    	   <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?>
    	</td>    	
    	<td>
    	   <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
    	   <?php echo htmlspecialchars($user->name.' '.$user->surname)?>		
    	   <?php endif; ?>
    	</td>
	</tr>
</table>
