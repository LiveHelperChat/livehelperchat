<?php $currentUser = erLhcoreClassUser::instance(); ?>

<div class="btn-group float-end" role="group" aria-label="...">
      <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
      <a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="btn btn-secondary btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
      <?php endif; ?>
      <?php if ($currentUser->hasAccessTo('lhchat','allowclearonlinelist')) : ?>
      <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(clear_list)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Clear list');?></a></li>
      <?php endif; ?>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_users_title.tpl.php')); ?>

<?php if ($tracking_enabled == false) : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User tracking is disabled, enable it at');?>&nbsp;-&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/track_online_visitors"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat configuration');?></a></p>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_general.tpl.php')); ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_online_check.tpl.php')); ?>

<div ng-controller="OnlineCtrl as online" ng-init='groupByField = <?php echo json_encode($ogroupBy, JSON_HEX_APOS)?>;online.maxRows="<?php echo (int)$omaxRows?>";online.updateTimeout="<?php echo (int)$oupdTimeout?>";online.time_on_site = <?php echo json_encode($oTimeOnSite,JSON_HEX_APOS)?>;online.userTimeout = "<?php echo (int)$ouserTimeout?>";online.online_connected=<?php echo $onlineVisitorOnly == 1 ? 'true' : 'false' ?>;online.department_dpgroups = <?php echo json_encode($onlineDepartmentGroups,JSON_HEX_APOS)?>;online.department=<?php echo json_encode($onlineDepartment,JSON_HEX_APOS)?>;online.country=<?php echo json_encode($oCountry,JSON_HEX_APOS)?>;online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false'?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false'?>;online.initController();'>

<div role="tabpanel" id="tabs">
	<!-- Nav tabs -->
	<ul class="nav nav-pills" role="tablist">
		<li role="presentation" class="nav-item"><a class="active nav-link" href="#onlineusers" aria-controls="onlineusers" role="tab" data-bs-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors list');?>"><i class="material-icons">face</i></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link" id="map-activator" href="#map" aria-controls="map" role="tab" data-bs-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors on map');?>"><i class="material-icons">place</i></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content" id="online-users-dashboard">
		<div role="tabpanel" class="tab-pane active" id="onlineusers">
		      <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users.tpl.php')); ?>
		</div>
		<div role="tabpanel" class="tab-pane" id="map">
		      <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online.tpl.php')); ?>
		</div>
	</div>
</div>


</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>
<script>

$( document ).ready(function() {
	lhinst.attachTabNavigator();
	$('#right-column-page').removeAttr('id');	
});
</script>
