<div class="row">
	<div class="columns large-7">
		<div class="message-block pb10">
			<div class="msgBlock">
				<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>

				<?php if ($chat->user_status == 1) : ?>
					<?php include(erLhcoreClassDesign::designtpl('lhchat/userleftchat.tpl.php')); ?>
				<?php elseif ($chat->user_status == 0) : ?>
					<?php include(erLhcoreClassDesign::designtpl('lhchat/userjoined.tpl.php')); ?>
				<?php endif;?>
			</div>

			<br>
			
			<div class="btn-group">
			  <a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/listarchivechats')?>/<?php echo $archive->id?>" class="btn btn-primary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Return')?></a>
			  <a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/deletearchivechat')?>/<?php echo $archive->id?>/<?php echo $chat->id?>" class="csfr-required btn btn-default" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?')?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat')?></a>
			</div>

			<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

		</div>
	</div>
	<div class="columns large-5">
		<?php $hideActionBlock = true;?>
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>		
	</div>
</div>