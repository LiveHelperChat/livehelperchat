<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <?php
    $permissionsWidget = [];
    if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','statisticdep')){
        $permissionsWidget[] = 'lhstatistic_statisticdep';
    }
    ?>
    <lhc-widget <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> permissions='<?php echo json_encode($permissionsWidget);?>' <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> type="active_chats" sort_identifier="active_chats_sort" icon_class="chat-active" type="active_chats" status_id="1" expand_identifier="activec_widget_exp" list_identifier="active-chats" panel_list_identifier="actived-panel-list" optionsPanel={"panelid":"actived","limitid":"limita","userid":"activeu"} www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>"></lhc-widget>

    <?php /*
	<div class="card card-dashboard card-active-chats" data-panel-id="active_chats" ng-init="lhc.getToggleWidget('activec_widget_exp');lhc.getToggleWidgetSort('active_chats_sort');">
		<div class="card-header">
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/1"><i class="material-icons chat-active">chat</i> <span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/active_chats.tpl.php'));?></span> ({{active_chats.list.length}}{{active_chats.list.length == lhc.limita ? '+' : ''}})</a>

			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('activec_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['activec_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

            <?php $takenTimeAttributes = 'active_chats.tt';?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
		</div>

		<div ng-if="lhc.toggleWidgetData['activec_widget_exp'] !== true">

			<?php $optinsPanel = array('panelid' => 'actived','limitid' => 'limita', 'userid' => 'activeu'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

			<div ng-if="active_chats.list.length > 0" id="actived-panel-list" class="panel-list" ng-style="{'maxHeight': lhc.actived_m_h}">
				<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/active.tpl.php'));?>
			</div>

			<div ng-if="active_chats.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','All active chats will appear here.')?></div>

		</div>
	</div>*/ ?>


<?php endif; ?>