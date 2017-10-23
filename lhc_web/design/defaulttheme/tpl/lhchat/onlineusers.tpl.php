<?php $currentUser = erLhcoreClassUser::instance(); ?>

<div class="btn-group pull-right" role="group" aria-label="...">
      <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
      <a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="btn btn-default btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
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

<div ng-controller="OnlineCtrl as online" ng-init='groupByField = <?php echo json_encode($ogroupBy)?>;online.maxRows=<?php echo (int)$omaxRows?>;online.updateTimeout=<?php echo (int)$oupdTimeout?>;online.userTimeout = <?php echo (int)$ouserTimeout?>;online.department=<?php echo (int)$onlineDepartment?>;online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false'?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false'?>'>

<div role="tabpanel" id="tabs">
	<!-- Nav tabs -->
	<ul class="nav nav-pills" role="tablist">
		<li role="presentation" class="active"><a href="#onlineusers" aria-controls="onlineusers" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors list');?>"><i class="material-icons">face</i></a></li>
		<li role="presentation"><a id="map-activator" href="#map" aria-controls="map" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors on map');?>"><i class="material-icons">place</i></a></li>
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
	<?php if (!isset($popoverInitialized) || $popoverInitialized == false) : ?>
	$('#dashboard-body, #onlineusers, #map').popover({
		  trigger:'hover',
		  html : true, 
		  container: 'body',
		  selector: '[data-toggle="popover"]',
		  content: function () {
			 if ($(this).is('[data-popover-content]')) {
				return $('#'+$(this).attr('data-popover-content')+'-'+$(this).attr('data-chat-id')).html();
			 } else {
				return $('#popover-content-'+$(this).attr('data-chat-id')).html();
			 }
		  },
		  title: function () {
			 return  $('#popover-title-'+$(this).attr('data-chat-id')).html();
		  }
	});
	<?php endif; ?>
		
	lhinst.attachTabNavigator();
	$('#right-column-page').removeAttr('id');	
});
</script>
