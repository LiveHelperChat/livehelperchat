<div class="right operator-info pt5">
	<i class="icon-thumbs-up<?php if ($chat->fbst == 1) : ?> up-voted<?php endif;?>"></i>
	<i class="icon-thumbs-down<?php if ($chat->fbst == 2) : ?> down-voted<?php endif;?>"></i>

	<span class="radius secondary label fs11" id="chat-status-text-<?php echo $chat->id?>">
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

<h5>
	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?>
</h5>

<table class="small-12">

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
		<td><div class="page-url"><span><?php echo $chat->referrer != '' ? '<a title="' . htmlspecialchars($chat->referrer) . '" href="' .htmlspecialchars($chat->referrer). '">'.htmlspecialchars($chat->referrer).'</a>' : ''?></span></div></td>
	</tr>
	<?php endif;?>

	<?php if (!empty($chat->session_referrer)) : ?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?></td>
		<td><div class="page-url"><span><?php echo $chat->session_referrer != '' ? '<a title="' . htmlspecialchars($chat->session_referrer) . '" href="' . htmlspecialchars($chat->session_referrer) . '">'.htmlspecialchars($chat->session_referrer).'</a>' : ''?></span></div></td>
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
		<td><?php echo htmlspecialchars($chat->additional_data)?></td>
	</tr>
	<?php endif;?>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created')?></td>
		<td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?></td>
	</tr>
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
</table>


<div class="row">
	<div class="columns small-6">

	<?php if (!isset($hideActionBlock)) : ?>
	<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Actions')?></h5>
	<p>
		<a class="icon-popup" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatCloseTabNewWindow('<?php echo $chat->id;?>',$('#tabs'),$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">
					
		<a class="icon-cancel" onclick="lhinst.removeDialogTab('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>"></a>
		<a class="icon-cancel-circled" onclick="lhinst.closeActiveChatDialog('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>"></a>
		<a class="icon-cancel-squared" onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>"></a>
			
		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat', 'allowtransfer')) : ?>
		<a class="icon-users" onclick="lhinst.transferUserDialog('<?php echo $chat->id?>',$(this).attr('title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
		<?php endif; ?>

		<a class="icon-block" data-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?')?>" onclick="lhinst.blockUser('<?php echo $chat->id?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>"></a>
			
		<a class="icon-mail <?php if ($chat->mail_send == 1) : ?>icon-mail-send<?php endif; ?>" onclick="lhinst.sendMail('<?php echo $chat->id?>')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?>"></a>
			
		<a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchatadmin')?>/<?php echo $chat->id?>" class="icon-print" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Print')?>"></a>
		
		<a class="icon-attach" onclick="lhinst.attatchLinkToFile('<?php echo $chat->id?>')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Attatch uploaded file')?>"></a>
		
	</p>
	<?php else : ?>
	<a class="icon-print" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/printchatadmin')?>/<?php echo $archive->id?>/<?php echo $chat->id?>"></a>
	<a class="icon-mail" onclick="return lhinst.sendMailArchive('<?php echo $archive->id?>','<?php echo $chat->id?>')"></a>
	<?php endif; ?>


	</div>
	<div class="columns small-6">

	<?php if ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
	<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat between operators, chat initializer')?></h5>
	<p><?php echo htmlspecialchars($chat->nick)?></p>
	<?php endif;?>

	<h5>
	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?>
	</h5>
	<p>
		<?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
		<?php echo htmlspecialchars($user->name.' '.$user->surname)?>		
		<?php endif; ?>
		</p>


		</div>
	</div>