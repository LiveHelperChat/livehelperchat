<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','New chat notification settings');?></h1>

<div class="row">
	<div class="columns large-2">
		<input type="button" class="button radius" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Request notification permission')?>" onclick="lhinst.requestNotificationPermission()" />
	</div>
	<div class="columns large-2 end">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
	</div>
</div>
