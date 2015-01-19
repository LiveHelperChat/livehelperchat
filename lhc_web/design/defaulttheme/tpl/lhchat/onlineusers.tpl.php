<?php $currentUser = erLhcoreClassUser::instance(); ?>

<ul class="button-group radius geo-settings">
      <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
      <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="button secondary tiny"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
      <?php endif; ?>
      <?php if ($currentUser->hasAccessTo('lhchat','allowclearonlinelist')) : ?>
      <li><a class="tiny button alert csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(clear_list)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Clear list');?></a></li>
      <?php endif; ?>
</ul>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors');?></h1>

<?php if ($tracking_enabled == false) : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User tracking is disabled, enable it at');?>&nbsp;-&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/track_online_visitors"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat configuration');?></a></p>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_general.tpl.php')); ?>

<div ng-controller="OnlineCtrl as online" ng-init='groupByField = <?php echo json_encode($ogroupBy)?>;online.maxRows=<?php echo (int)$omaxRows?>;online.updateTimeout=<?php echo (int)$oupdTimeout?>;online.userTimeout = <?php echo (int)$ouserTimeout?>;online.department=<?php echo (int)$onlineDepartment?>;online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false'?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false'?>'>

<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings.tpl.php')); ?>

<div class="section-container auto" data-section="auto" id="tabs" data-options="deep_linking: true">
     <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users.tpl.php')); ?>
     <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online.tpl.php')); ?>
</div>

</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>
<script>
$( document ).ready(function() {
	lhinst.attachTabNavigator();
	$('#right-column-page').removeAttr('id');
});
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/opened_chats_js.tpl.php')); ?>
</script>