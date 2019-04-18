<div class="card card-dashboard" data-panel-id="departments_stats" ng-init="lhc.getToggleWidget('dstats_widget_exp')">
	<div class="card-header">
		<i class="material-icons chat-active">&#xf2dc;</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/departmetns_stats.tpl.php'));?> ({{departments_stats.list.length}}{{departments_stats.list.length == lhc.limitd ? '+' : ''}})</a>

		<?php if ($currentUser->hasAccessTo('lhstatistic', 'exportxls')) : ?><a class="material-icons" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('statistic/departmentstatusxls')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Download XLS');?>">&#xf964;</a><?php endif;?>

		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('dstats_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['dstats_widget_exp'] == false ? '&#xf143;' : '&#xf140;'}}</a>

	</div>
	<div ng-if="lhc.toggleWidgetData['dstats_widget_exp'] !== true">

	    <?php $optinsPanel = array('panelid' => 'departmentd','limitid' => 'limitd', 'disable_product' => true); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

		<div class="panel-list">
			<table class="table table-sm mb-0 table-small table-fixed">
				<thead>
					<tr>
						<th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">&#xf2dc;</i></th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Pending chats');?>" class="material-icons chat-pending">&#xfb55;</i></th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats');?>" class="material-icons chat-active">&#xfb55;</i></th>
					</tr>
				</thead>
				<tr ng-repeat="department in departments_stats.list track by department.id">
					<td>
						<div class="abbr-list" title="{{department.name}}">{{department.name}}</div>
					</td>
					<td>{{department.pending_chats_counter ? department.pending_chats_counter : 0}}</td>
					<td>{{department.active_chats_counter ? department.active_chats_counter : 0}}</td>
				</tr>
			</table>
		</div>
	</div>
</div>
