<div class="card card-dashboard" data-panel-id="online_visitors" ng-init="lhc.getToggleWidget('onvisitors_widget_exp')">
	<div class="card-header">
		<i class="material-icons">face</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/online_visitors.tpl.php'));?> ({{online.onlineusers.length}})
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('onvisitors_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['onvisitors_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
	</div>
	<div ng-if="lhc.toggleWidgetData['onvisitors_widget_exp'] !== true">

        <div class="p-2">
           <div class="row">
               <div class="col-3 pr-0">
                    <input class="form-control form-control-sm" ng-model="query" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Type to search')?>">
               </div>
                <div class="col-3 pr-0">
                    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                        'input_name'     => 'department_id',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
                        'selected_id'    => 0,
                        'css_class'      => 'form-control form-control-sm',
                        'ng-model'		 => 'online.department',
                        'list_function'  => 'erLhcoreClassModelDepartament::getList',
                        'list_function_params' => $departmentParams
                    )); ?>
                </div>
               <?php $columnCountrySize = 3?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/country_filter.tpl.php')); ?>

               <?php $columnCountrySize = 3;?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/time_on_site_filter.tpl.php')); ?>
           </div>
        </div>

	   <div class="panel-list" ng-if="online.onlineusers.length > 0">
			<table ng-cloak class="table table-sm mb-0 table-small table-fixed" cellpadding="0" cellspacing="0" ng-init='trans = <?php echo json_encode(array('third' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has not seen a message from the operator, or the message window is still open.'),'msg_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Seen'),'msg_not_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Unseen'),'second' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.'),'first' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator')),JSON_HEX_APOS)?>'>
				<thead>
                    <th width="50%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Main information')?></th>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header_online.tpl.php'));?>
                </thead>
                <tbody ng-repeat="group in onlineusersGrouped track by group.id">

					<tr ng-show="group.label != ''">
						<td><h5 class="group-by-{{groupByField}}">{{group.label}} ({{group.ou.length}})</h5></td>
      				</tr>
					<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id" ng-class="{recent_visit:(ou.last_visit_seconds_ago < 15)<?php echo $onlineCheck?>}">
						<td nowrap="nowrap">
							<div class="btn-group" role="group" aria-label="...">
								<a href="#" class="btn btn-xs btn-secondary" ng-class="{'icon-user-away': ou.online_status == 1, 'icon-user-online': ou.online_status == 0}" ng-click="online.showOnlineUserInfo(ou.id)"><i class="material-icons">info_outline</i>{{ou.lastactivity_ago}} | {{ou.nick}}&nbsp;<img ng-if="ou.user_country_code" ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" /></a><?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/custom_online_button_multiinclude.tpl.php')); ?><span ng-click="online.previewChat(ou)" class="btn btn-xs btn-success action-image" ng-show="ou.chat_id > 0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Preview chat')?>"><i class="material-icons mr0">chat</i></span><span class="btn btn-xs btn-info" ng-show="ou.total_visits > 1"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visits');?> ({{ou.total_visits}})</span><span class="btn btn-success btn-xs" ng-show="ou.total_visits == 1"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New');?></span> <span title="{{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" class="btn btn-xs" ng-show="ou.operator_message" ng-class="ou.message_seen == 1 ? 'btn-success' : 'btn-danger'"><i class="material-icons">chat_bubble_outline</i>{{ou.message_seen == 1 ? trans.msg_seen : trans.msg_not_seen}}</span>
							</div>
							<div class="abbr-list" ng-if="ou.page_title || ou.current_page">
								<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?>">&#xE8A0;</i><a target="_blank" rel="noopener" href="{{ou.current_page}}" title="{{ou.current_page}}">{{ou.page_title || ou.current_page}}</a>
							</div>
							<div class="abbr-list" ng-if="ou.referrer">
								<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','From');?>">&#xE8A0;</i><a target="_blank" rel="noopener" href="http:{{ou.referrer}}" title="{{ou.referrer}}">{{ou.referrer}}</a>
							</div>
						</td>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body_online.tpl.php'));?>
					</tr>
				</tbody>
			</table>
		</div>
		<div ng-if="online.onlineusers.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','You will see short list of your site visitors here.')?>...</div>
	</div>
</div>
