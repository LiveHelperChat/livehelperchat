<div class="card card-dashboard" data-panel-id="departments_stats" ng-init="lhc.getToggleWidget('dstats_widget_exp')">
	<div class="card-header">
		<i class="material-icons chat-active">home</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/departmetns_stats.tpl.php'));?></a>
		<?php if ($currentUser->hasAccessTo('lhstatistic', 'exportxls')) : ?><a class="material-icons" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('statistic/departmentstatusxls')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Download XLS');?>">file_download</a><?php endif;?>
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('dstats_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['dstats_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
	</div>
	<div ng-if="lhc.toggleWidgetData['dstats_widget_exp'] !== true">

	    <?php $optinsPanel = array('panelid' => 'departmentd','limitid' => 'limitd', 'disable_product' => true,'hide_department' => true, 'hide_depgroup' => true); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

		<div class="panel-list">
			<table class="table table-sm mb-0 table-small table-fixed">
                <thead ng-if="!lhc.departmentd_hide_dgroup && depgroups_stats.list.length > 0">
                <tr>
                    <th width="40%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department group');?>" class="material-icons">&#xE84F;</i></th>
                    <th width="12%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Pending chats');?>" class="material-icons chat-pending">chat</i></th>
                    <th width="12%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats');?>" class="material-icons chat-active">chat</i></th>
                    <th width="12%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Bot chats');?>" class="material-icons chat-active">android</i></th>
                    <th width="21%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Load statistic');?>" class="material-icons text-info">donut_large</i></th>
                </tr>
                </thead>
                <tr ng-if="!lhc.departmentd_hide_dgroup" ng-repeat="depgroup in depgroups_stats.list track by depgroup.id">
                    <td>
                        <div class="abbr-list" title="{{depgroup.name}}"><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_group_ids)/{{depgroup.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT,'/',erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,'/',erLhcoreClassModelChat::STATUS_BOT_CHAT?>">{{depgroup.name}}</a></div>
                    </td>
                    <td><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_group_ids)/{{depgroup.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT ?>">{{depgroup.pchats_cnt ? depgroup.pchats_cnt : 0}}</a></td>
                    <td><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_group_ids)/{{depgroup.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_ACTIVE_CHAT ?>">{{depgroup.achats_cnt ? depgroup.achats_cnt : 0}}</a></td>
                    <td><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_group_ids)/{{depgroup.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_BOT_CHAT ?>">{{depgroup.bchats_cnt ? depgroup.bchats_cnt : 0}}</a></td>
                    <td nowrap title="{{depgroup.inachats_cnt ? depgroup.inachats_cnt : '0'}} inactive chats.<?php echo "\n"?>{{depgroup.inopchats_cnt ? depgroup.inopchats_cnt : '0'}} inactive online operators chats.<?php echo "\n"?>{{depgroup.acopchats_cnt ? depgroup.acopchats_cnt : '0'}} active online operators chats.<?php echo "\n"?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hard limit');?> - {{depgroup.max_load_h ? depgroup.max_load_h : '0'}}, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Soft limit');?> - {{depgroup.max_load ? depgroup.max_load : '0'}}.<?php echo "\n"?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hard limit - (active online operators chats - inactive online operators chats) (soft limit - active chats)');?>">
                        <?php if ($currentUser->hasAccessTo('lhstatistic','statisticdep')) : ?><a href="#" ng-click="lhc.openModal('statistic/departmentstats/'+depgroup.id+'/(type)/group')" ><?php endif; ?>
                            <span ng-class="{'text-danger font-weight-bold': (depgroup.max_load_h && depgroup.max_load_h - (depgroup.acopchats_cnt - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0)) <= 3)}">{{depgroup.max_load_h ? (depgroup.max_load_h - (depgroup.acopchats_cnt - (depgroup.inopchats_cnt ? depgroup.inopchats_cnt : 0))) : 'n/a'}}</span>&nbsp;({{depgroup.max_load ? (depgroup.max_load - (depgroup.achats_cnt - (depgroup.inachats_cnt ? depgroup.inachats_cnt : 0))) : 'n/a'}})
                        <?php if ($currentUser->hasAccessTo('lhstatistic','statisticdep')) : ?></a><?php endif; ?>
                    </td>
                </tr>
				<thead ng-if="!lhc.departmentd_hide_dep && departments_stats.list.length > 0">
					<tr>
						<th width="40%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
						<th width="12%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Pending chats');?>" class="material-icons chat-pending">chat</i></th>
						<th width="12%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats');?>" class="material-icons chat-active">chat</i></th>
						<th width="12%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Bot chats');?>" class="material-icons chat-active">android</i></th>
						<th width="21%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Load statistic');?>" class="material-icons text-info">donut_large</i></th>
					</tr>
				</thead>
				<tr ng-if="!lhc.departmentd_hide_dep" ng-repeat="department in departments_stats.list track by department.id">
					<td>
                        <div class="abbr-list" title="{{department.name}}"><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_ids)/{{department.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT,'/',erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,'/',erLhcoreClassModelChat::STATUS_BOT_CHAT?>">{{department.name}}</a></div>
					</td>
					<td><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_ids)/{{department.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT ?>">{{department.pending_chats_counter ? department.pending_chats_counter : 0}}</a></td>
                    <td><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_ids)/{{department.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_ACTIVE_CHAT ?>" >{{department.active_chats_counter ? department.active_chats_counter : 0}}</a></td>
                    <td><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(department_ids)/{{department.id}}/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_BOT_CHAT ?>">{{department.bot_chats_counter ? department.bot_chats_counter : 0}}</a></td>
                    <td nowrap title="{{department.inactive_chats_cnt ? department.inactive_chats_cnt : '0'}} inactive chats.<?php echo "\n"?>{{department.inop_chats_cnt ? department.inop_chats_cnt : '0'}} inactive online operators chats.<?php echo "\n"?>{{department.acop_chats_cnt ? department.acop_chats_cnt : '0'}} active online operators chats.<?php echo "\n"?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hard');?> - {{department.max_load_h ? department.max_load_h : '0'}}, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Soft');?> - {{department.max_load ? department.max_load : '0'}}.<?php echo "\n"?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hard limit - (active online operators chats - inactive online operators chats) (soft limit - active chats)');?>">

                       <?php if ($currentUser->hasAccessTo('lhstatistic','statisticdep')) : ?><a href="#" ng-click="lhc.openModal('statistic/departmentstats/'+department.id)"><?php endif; ?>
                            <span ng-class="{'text-danger font-weight-bold': (department.max_load_h && department.max_load_h - (department.acop_chats_cnt - (department.inop_chats_cnt ? department.inop_chats_cnt : 0)) <= 3)}">{{department.max_load_h ? (department.max_load_h - (department.acop_chats_cnt - (department.inop_chats_cnt ? department.inop_chats_cnt : 0))) : 'n/a'}}</span>&nbsp;({{department.max_load ? (department.max_load - (department.active_chats_counter - (department.inactive_chats_cnt ? department.inactive_chats_cnt : 0))) : 'n/a'}})
                       <?php if ($currentUser->hasAccessTo('lhstatistic','statisticdep')) : ?></a><?php endif; ?>

                    </td>


				</tr>
			</table>
		</div>
	</div>
</div>
