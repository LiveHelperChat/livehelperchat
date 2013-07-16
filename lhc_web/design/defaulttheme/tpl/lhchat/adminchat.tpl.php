<div class="row">
	<div class="columns large-7">

		<div class="message-block">
			<div class="msgBlock" id="messagesBlock-<?php echo $chat->id?>">
				<?php
				$LastMessageID = 0;
    $messages = erLhcoreClassChat::getChatMessages($chat->id); ?>

				<?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
				<?php if (isset($msg)) {
					$LastMessageID = $msg['id'];
} ?>

				<?php if ($chat->user_status == 1) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userleftchat.tpl.php')); ?>
				<?php elseif ($chat->user_status == 0) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userjoined.tpl.php')); ?>
				<?php endif;?>

			</div>
			<div class="user-is-typing"
				id="user-is-typing-<?php echo $chat->id?>">
				<i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','User is typing now...')?>
				</i>
			</div>
		</div>
		<br />

		<textarea rows="4" name="ChatMessage"
			id="CSChatMessage-<?php echo $chat->id?>"></textarea>
		<script type="text/javascript">
jQuery('#CSChatMessage-<?php echo $chat->id?>').bind('keyup', 'return', function (evt){
    lhinst.addmsgadmin('<?php echo $chat->id?>');
});
lhinst.initTypingMonitoringAdmin('<?php echo $chat->id?>');
</script>

		<div class="row">
			<div class="columns small-4">
				<input type="button"
					value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>"
					class="small button round"
					onclick="lhinst.addmsgadmin('<?php echo $chat->id?>')" />
			</div>
			<div class="columns small-8">
				<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
						'input_name'     => 'CannedMessage-'.$chat->id,
						'on_change'      => "$('#CSChatMessage-".$chat->id."').val(($(this).val() > 0) ? $(this).find(':selected').text() : '')",
						'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message'),
						'display_name'   => 'msg',
						'selected_id'    => '',
						'list_function'  => 'erLhcoreClassModelCannedMsg::getList'
            )); ?>
			</div>
		</div>
	</div>
	<div class="columns large-5">


		<div class="section-container auto" data-section>
			<section>
				<p class="title" data-section-title>
					<a href="#panel1">Visitor</a>
				</p>
				<div class="content" data-section-content>

					<h5>
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?>
					</h5>

					<table class="small-12">
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Value</th>
							</tr>
						</thead>
						<?php if ( !empty($chat->country_code) ) : ?>
						<tr>
							<td>Country</td>
							<td><img
							src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png"
							alt="<?php echo htmlspecialchars($chat->country_name)?>"
							title="<?php echo htmlspecialchars($chat->country_name)?>" /></td>
						</tr>
						<?php endif;?>
						<tr>
							<td>IP</td>
							<td><?php echo $chat->ip?></td>
						</tr>
						<?php if (!empty($chat->referrer)) : ?>
						<tr>
							<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?></td>
							<td><?php echo $chat->referrer != '' ? htmlspecialchars($chat->referrer) : ''?></td>
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
					</table>

					<h5>
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?>
					</h5>
					<p>
						<?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
						<?php echo htmlspecialchars($user->name)?>
						<?php echo htmlspecialchars($user->surname)?>
						<?php endif; ?>
					</p>


					<h5>
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Actions')?>
					</h5>
					<p>
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
						<img class="action-image" align="absmiddle"
							onclick="lhinst.transferUserDialog('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>')"
							src="<?php echo erLhcoreClassDesign::design('images/icons/user_go.png');?>"
							alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>"
							title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
						<img class="action-image" align="absmiddle"
							onclick="lhinst.blockUser('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?')?>')"
							src="<?php echo erLhcoreClassDesign::design('images/icons/user_delete.png');?>"
							alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>"
							title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>">
						<img class="action-image" align="absmiddle"
							onclick="lhinst.sendMail('<?php echo $chat->id?>')"
							src="<?php if ($chat->mail_send == 0) : ?><?php echo erLhcoreClassDesign::design('images/icons/email.png');?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/email-send.png');?><?php endif; ?>"
							alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?>"
							title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send mail')?>">
					</p>

				</div>
			</section>
			<section>
				<p class="title" data-section-title>
					<a href="#panel2">Footprint</a>
				</p>
				<div class="content" data-section-content>
					<p>Footprint location</p>
				</div>
			</section>
			<section>
				<p class="title" data-section-title>
					<a href="#panel2">Map</a>
				</p>
				<div class="content" data-section-content>
					<p>Location in map</p>
				</div>
			</section>
		</div>

	</div>
</div>



<script type="text/javascript">
lhinst.addSynchroChat('<?php echo $chat->id;?>','<?php echo $LastMessageID?>');

$('#messagesBlock-<?php echo $chat->id?>').animate({ scrollTop: $('#messagesBlock-<?php echo $chat->id?>').prop('scrollHeight') }, 1000);

// Start synchronisation
lhinst.startSyncAdmin();
</script>
