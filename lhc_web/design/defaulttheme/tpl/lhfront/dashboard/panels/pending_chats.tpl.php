<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
	<div class="panel panel-default panel-dashboard" data-panel-id="pending_chats" ng-init="lhc.getToggleWidget('pchats_widget_exp');lhc.getToggleWidget('pending_chats_sort')">
		<div class="panel-heading">
			<a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/0"><i class="material-icons chat-pending">chat</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/pending_chats.tpl.php'));?> ({{pending_chats.list.length}}{{pending_chats.list.length == lhc.limitp ? '+' : ''}})</a>
			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('pchats_widget_exp')" class="fs24 pull-right material-icons exp-cntr">{{lhc.toggleWidgetData['pchats_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
		</div>
		<div ng-if="lhc.toggleWidgetData['pchats_widget_exp'] !== true">

			  <?php $optinsPanel = array('panelid' => 'pendingd','limitid' => 'limitp', 'userid' => 'pendingu'); ?>
			  <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

			  <div class="panel-list" ng-if="pending_chats.list.length > 0">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/pending.tpl.php'));?>
              </div>

			<div ng-if="pending_chats.list.length == 0" class="m10 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>

		</div>
	</div>
<?php endif; ?>