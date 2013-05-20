<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','New chat notification settings');?></h1>

<input type="button" class="button radius" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Request notification permission')?>" onclick="lhinst.requestNotificationPermission()" />

<div class="row">
	<div class="columns small-2 end">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
	</div>
</div>
