


<?php
$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
$mychatsTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_mchats_list',1);
?>

<div role="tabpanel" ng-cloak id="tabs">



	<!-- Nav tabs -->
	<ul class="nav nav-pills" role="tablist">

        <?php $hideULSetting = true; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php')); ?>

	    <?php if ($pendingTabEnabled == true) : ?>
		<li role="presentation" class="nav-item"><a class="nav-link active" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?>" href="#panel1" aria-controls="panel1" role="tab" data-bs-toggle="tab"><i class="material-icons chat-pending me-0">chat</i></a></li>
		<?php endif;?>
		
		<?php if ($activeTabEnabled == true) : ?>
		<li role="presentation" class="nav-item"><a class="nav-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?>" href="#panel2" aria-controls="panel2" role="tab" data-bs-toggle="tab"><i class="material-icons chat-active me-0">chat</i></a></li>
		<?php endif;?>
		
		<?php if ($mychatsTabEnabled == true) : ?>
		<li role="presentation" class="nav-item"><a class="nav-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','My active and pending chats');?>" href="#panel3" aria-controls="panel3" role="tab" data-bs-toggle="tab"><i class="material-icons chat-active me-0">chat</i></a></li>
		<?php endif;?>

		<?php if ($unreadTabEnabled == true) : ?>
		<li role="presentation" class="nav-item"><a class="nav-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?>" href="#panel4" aria-controls="panel4" role="tab" data-bs-toggle="tab"><i class="material-icons chat-unread me-0">chat</i></a></li>
		<?php endif;?>

        <div class="position-absolute pe-2" style="right: 0">
            <lhc-connection-status></lhc-connection-status>
        </div>

	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
	    <?php if ($pendingTabEnabled == true) : ?>
		<div role="tabpanel" class="tab-pane active" id="panel1">
		      <div id="pending-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?></div>
			  <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
		</div>
		<?php endif;?>
		<?php if ($activeTabEnabled == true) : ?>
		<div role="tabpanel" class="tab-pane" id="panel2">
		     <div id="active-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?></div>
			 <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
		</div>
		<?php endif;?>

		<?php if ($mychatsTabEnabled == true) : ?>
		<div role="tabpanel" class="tab-pane" id="panel3">
		     <div id="active-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_my_chats_list.tpl.php'));?></div>
			 <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','My active and pending chats');?></a>
		</div>
		<?php endif;?>
		
		<?php if ($unreadTabEnabled == true) : ?>
		<div role="tabpanel" class="tab-pane" id="panel4">
		     <div id="unread-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?></div>
			 <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
		</div>
		<?php endif;?>
		
	</div>
</div>

<script type="text/javascript">
function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    window.focus();
};

lhinst.attachTabNavigator();
</script>
