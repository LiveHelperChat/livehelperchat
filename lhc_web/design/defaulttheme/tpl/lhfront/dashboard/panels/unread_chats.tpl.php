<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
	<div class="card card-dashboard" data-panel-id="unread_chats" ng-init="lhc.getToggleWidget('unchats_widget_exp')">
		<div class="card-header">
			<a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><i class="material-icons chat-unread">chat</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/unread_chats.tpl.php'));?> ({{unread_chats.list.length}}{{unread_chats.list.length == lhc.limitu ? '+' : ''}})</a>
			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('unchats_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['unchats_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
		</div>
		<div ng-if="lhc.toggleWidgetData['unchats_widget_exp'] !== true">

			<?php $optinsPanel = array('panelid' => 'unreadd','limitid' => 'limitu'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
            
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/unread_chats.tpl.php'));?>

            <div ng-if="unread_chats.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>

		</div>
	</div>
<?php endif; ?>