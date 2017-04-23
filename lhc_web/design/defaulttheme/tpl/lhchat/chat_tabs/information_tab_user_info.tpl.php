<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information/information_top.tpl.php'));?>

<div class="pull-right operator-info pt5">
	<i class="material-icons mr-0<?php if ($chat->fbst == 1) : ?> up-voted<?php endif;?>">thumb_up</i>
	<i class="material-icons<?php if ($chat->fbst == 2) : ?> down-voted<?php endif;?>">thumb_down</i>

	<span class="pull-right label label-default fs12<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?> action-image<?php endif?>" id="chat-status-text-<?php echo $chat->id?>" <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Click to change chat status')?>" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/changestatus/<?php echo $chat->id?>'})"<?php endif;?>>
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

<a onclick="return lhc.revealModal({'iframe':true,'height':350,'url':WWW_DIR_JAVASCRIPT +'chat/modifychat/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Edit main chat information')?>" href="#"><i class="material-icons mr-0">mode_edit</i></a>

<?php if (!isset($hideActionBlock)) : ?>
				
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/open_new_window.tpl.php'));?>
				
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/show_survey.tpl.php'));?>
			
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/remove_dialog_tab.tpl.php'));?>	
		
		<?php if ($chat->user_id == erLhcoreClassUser::instance()->getUserID() || erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowcloseremote')) : ?>
		<a class="material-icons mr-0" onclick="lhinst.closeActiveChatDialog('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>">close</a>
		<?php endif;?>
		
		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deleteglobalchat') || (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deletechat') && $chat->user_id == erLhcoreClassUser::instance()->getUserID())) : ?>
		<a class="material-icons mr-0" onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>">delete</a>
		<?php endif ?>	
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer.tpl.php'));?>
		
		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowblockusers')) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser.tpl.php'));?>
		<?php endif;?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/send_mail.tpl.php'));?>
		
        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_contact.tpl.php'));?>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/print.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/attatch_file.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_user.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/speech.tpl.php'));?>
			
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/cobrowse.tpl.php'));?>
				
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_actions_extension_multiinclude.tpl.php'));?>
		 	
	<?php else : ?>
	<a class="material-icons mr-0" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/printchatadmin')?>/<?php echo $archive->id?>/<?php echo $chat->id?>">print</a>
	<a class="material-icons mr-0" onclick="return lhc.revealModal({'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'chatarchive/sendmail/<?php echo $archive->id?>/<?php echo $chat->id?>'})">mail</a>
	<?php endif; ?>	
</div>


<table class="table table-condensed">

    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/above_department_extension_multiinclude.tpl.php'));?>

	<?php if ( $chat->department !== false ) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Department')?></td>
		<td><?php echo htmlspecialchars($chat->department);?></td>
	</tr>
	<?php endif;?>
	
	<?php if ($chat->product !== false) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Product')?></td>
		<td><?php echo htmlspecialchars($chat->product);?></td>
	</tr>
	<?php endif;?>
	
	<?php if ( !empty($chat->uagent) ) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Device')?></td>
		<td><i class="material-icons" title="<?php echo htmlspecialchars($chat->uagent)?>"><?php echo ($chat->device_type == 0 ? 'computer' : ($chat->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($chat->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($chat->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?></td>
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
		<td><?php echo htmlspecialchars($chat->ip)?></td>
	</tr>
	<?php if (!empty($chat->referrer)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Page')?></td>
		<td><div class="page-url"><span><?php echo $chat->referrer != '' ? '<a target="_blank" rel="noopener" title="' . htmlspecialchars($chat->referrer) . '" href="' .htmlspecialchars($chat->referrer). '">'.htmlspecialchars($chat->referrer).'</a>' : ''?></span></div></td>
	</tr>
	<?php endif;?>

	<?php if (!empty($chat->session_referrer)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?></td>
		<td><div class="page-url"><span><?php echo $chat->session_referrer != '' ? '<a target="_blank" rel="noopener" title="' . htmlspecialchars($chat->session_referrer) . '" href="' . htmlspecialchars($chat->session_referrer) . '">'.htmlspecialchars($chat->session_referrer).'</a>' : ''?></span></div></td>
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
	
	<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/after_phone_extension_multiinclude.tpl.php'));?>
	
	<?php if (!empty($chat->additional_data)) : ?>
	<tr>
		<td>
		  <a class="btn btn-default btn-xs" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_cfrefresh');$('#custom-data-td-<?php echo $chat->id?>').text('...')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?>"><i class="material-icons mr-0">&#xE5D5;</i></a>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Additional data')?>
		</td>
		<td id="custom-data-td-<?php echo $chat->id?>">		
		<?php if (is_array($chat->additional_data_array)) : ?>
			<ul class="circle mb0">
				<?php foreach ($chat->additional_data_array as $addItem) : ?>
				<li><?php echo htmlspecialchars($addItem->key)?> - <?php echo htmlspecialchars($addItem->value)?><?php if (isset($addItem->h) && $addItem->h == true) : ?>&nbsp;<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hidden field')?>">visibility_off</i><?php endif;?></li>
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
