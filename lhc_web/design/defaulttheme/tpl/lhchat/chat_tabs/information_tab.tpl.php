<section>
<p class="title" data-section-title>
	<a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></a>
</p>
<div class="content overflow-x-scrollbar" data-section-content>

	<div class="right">
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
			<td><?php echo date('Y-m-d H:i:s',$chat->time)?></td>
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

	<h5>
		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Actions')?>
	</h5>
	<p>
		<img class="action-image" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" align="absmiddle" onclick="lhinst.startChatCloseTabNewWindow('<?php echo $chat->id;?>',$('#tabs'),$(this).attr('data-title'))" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">

		<img class="action-image" align="absmiddle"
			onclick="lhinst.removeDialogTab('<?php echo $chat->id?>',$('#tabs'),true)"
			src="<?php echo erLhcoreClassDesign::design('images/icons/application_delete.png');?>"
			alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>"
			title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>">

		<img class="action-image" align="absmiddle"
			onclick="lhinst.closeActiveChatDialog('<?php echo $chat->id?>',$('#tabs'),true)"
			src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>"
			alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>"
			title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>">
		<img class="action-image" align="absmiddle"
			onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)"
			src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>"
			alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>"
			title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>">

		<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat', 'allowtransfer')) : ?>
		<img class="action-image" align="absmiddle" onclick="lhinst.transferUserDialog('<?php echo $chat->id?>',$(this).attr('title'))" src="<?php echo erLhcoreClassDesign::design('images/icons/user_go.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>"	title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
		<?php endif; ?>

		<img class="action-image" align="absmiddle" data-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?')?>" onclick="lhinst.blockUser('<?php echo $chat->id?>',$(this).attr('data-title'))"
			src="<?php echo erLhcoreClassDesign::design('images/icons/user_delete.png');?>"
			alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>"
			title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>">
		<img class="action-image" align="absmiddle"
			onclick="lhinst.sendMail('<?php echo $chat->id?>')"
			src="<?php if ($chat->mail_send == 0) : ?><?php echo erLhcoreClassDesign::design('images/icons/email.png');?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/email-send.png');?><?php endif; ?>"
			alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?>"
			title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?>">


		<a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchatadmin')?>/<?php echo $chat->id?>" class="print-ico-admin" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Print')?>"></a>

	</p>


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
			<?php echo htmlspecialchars($user->name)?>
			<?php echo htmlspecialchars($user->surname)?>
			<?php endif; ?>
			</p>


			</div>
		</div>


	</div>
</section>