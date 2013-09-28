<?php if ( ($online_user = $chat->online_user) !== false) : ?>
<section>
	<p class="title" data-section-title>
		<a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Browsing information')?></a>
	</p>
	<div class="content" data-section-content>
		<div>
			<a class="tiny round button" rel="<?php echo $chat->id?>" onclick="lhinst.refreshOnlineUserInfo($(this))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?></a>

			<div id="online-user-info-<?php echo $chat->id?>">
				<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>
			</div>
		</div>

	</div>
</section>

<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats.tpl.php')); ?>

<?php endif; ?>