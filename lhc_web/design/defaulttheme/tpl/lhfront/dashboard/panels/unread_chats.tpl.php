<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <lhc-widget <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-unread" limit_list_identifier="limitu" type="unread_chats" status_id="1" status_key="hum" expand_identifier="unchats_widget_exp" list_identifier="unread-chats" height_identifier="unread_m_h" panel_list_identifier="unreadc-panel-list" optionsPanel='{"panelid":"unreadd","limitid":"limitu"}' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>"></lhc-widget>

<?php /*
	<div class="card card-dashboard card-unread" data-panel-id="unread_chats" ng-init="lhc.getToggleWidget('unchats_widget_exp')">
		<div class="card-header">
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><i class="material-icons chat-unread">chat</i> <span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/unread_chats.tpl.php'));?></span> ({{unread_chats.list.length}}{{unread_chats.list.length == lhc.limitu ? '+' : ''}})</a>

			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('unchats_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['unchats_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

            <?php $takenTimeAttributes = 'unread_chats.tt';?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
		</div>
		<div ng-if="lhc.toggleWidgetData['unchats_widget_exp'] !== true">

			<?php $optinsPanel = array('panelid' => 'unreadd','limitid' => 'limitu'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
            
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/unread_chats.tpl.php'));?>

            <div ng-if="unread_chats.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>

		</div>
	</div>*/ ?>

<?php endif; ?>