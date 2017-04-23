<div class="panel panel-default panel-dashboard" data-panel-id="online_visitors" ng-init="lhc.getToggleWidget('onvisitors_widget_exp')">
	<div class="panel-heading">
		<i class="material-icons">face</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/online_visitors.tpl.php'));?> ({{online.onlineusers.length}})
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('onvisitors_widget_exp')" class="fs24 pull-right material-icons exp-cntr">{{lhc.toggleWidgetData['onvisitors_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
	</div>
	<div ng-if="lhc.toggleWidgetData['onvisitors_widget_exp'] !== true">
	   <div class="p10">
	       <input class="form-control input-sm" ng-model="query" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Type to search')?>">
	   </div>
	   <div class="panel-list" ng-if="online.onlineusers.length > 0">
			<table ng-cloak class="table table-condensed mb0 table-small table-fixed" cellpadding="0" cellspacing="0" ng-init='trans = <?php echo json_encode(array('third' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has not seen a message from the operator, or the message window is still open.'),'msg_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Seen'),'msg_not_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Unseen'),'second' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.'),'first' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator')),JSON_HEX_APOS)?>'>
				<tbody ng-repeat="group in onlineusersGrouped track by group.id">
					<tr ng-show="group.label != ''">
						<td colspan="6"><h5 class="group-by-{{groupByField}}">{{group.label}} ({{group.ou.length}})</h5></td>
					</tr>
					<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id" ng-class="{recent_visit:(ou.last_visit_seconds_ago < 15)<?php echo $onlineCheck?>}">
						<td>
							<div class="btn-group" role="group" aria-label="...">
								<a class="btn btn-xs btn-default" ng-class="{'icon-user-away': ou.online_status == 1, 'icon-user-online': ou.online_status == 0}" data-popover-title="popover-title-ou" data-popover-content="popover-content-ou" data-container="body" data-chat-id="{{ou.id}}" data-toggle="popover" data-placement="right" ng-click="online.showOnlineUserInfo(ou.id)"><i class="material-icons">info_outline</i>{{ou.lastactivity_ago}}&nbsp;<img ng-if="ou.user_country_code != ''" ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" /></a><?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/custom_online_button_multiinclude.tpl.php')); ?><span ng-click="online.previewChat(ou)" class="btn btn-xs btn-success action-image" ng-show="ou.chat_id > 0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Preview chat')?>"><i class="material-icons mr0">chat</i></span><span class="btn btn-xs btn-info" ng-show="ou.total_visits > 1"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visits');?> ({{ou.total_visits}})</span><span class="btn btn-success btn-xs" ng-show="ou.total_visits == 1"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New');?></span> <span title="{{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" class="btn btn-xs" ng-show="ou.operator_message != ''" ng-class="ou.message_seen == 1 ? 'btn-success' : 'btn-danger'"><i class="material-icons">chat_bubble_outline</i>{{ou.message_seen == 1 ? trans.msg_seen : trans.msg_not_seen}}</span>
							</div>
                            <div id="popover-title-ou-{{ou.id}}" class="hide" ng-if="ou.user_country_code != ''">
    						   <span class="up-case-first"><i class="material-icons mr-0">place</i> {{ou.user_country_name}}{{ou.city != '' ? ' | '+ou.city : ''}}</span>
    						</div>
							<div id="popover-content-ou-{{ou.id}}" class="hide">
								<ul class="list-unstyled"> 
									<li><i class="material-icons">access_time</i>{{ou.visitor_tz}} - {{ou.visitor_tz_time}}</li>
									<li>{{ou.notes_intro}}
									<li><i class="material-icons">access_time</i>{{ou.first_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?>
									<li><i class="material-icons">access_time</i>{{ou.last_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?>
        	                        <li><i class="material-icons">&#xE8A0;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - {{ou.pages_count}} {{ou.identifier != '' ? ' Identifier - '+ou.identifier : ''}} | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?> {{ou.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?> | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?> <span class="fs-11">{{ou.time_on_site_front}}</span>
        	                        <li><i class="material-icons">&#xE1B1;</i>{{ou.user_agent}} | IP: {{ou.ip}}
								</ul>
							</div>
							<div class="abbr-list pt5" ng-if="ou.page_title || ou.current_page">
								<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?>">&#xE8A0;</i><a target="_blank" rel="noopener" href="{{ou.current_page}}" title="{{ou.current_page}}">{{ou.page_title || ou.current_page}}</a>
							</div>
							<div class="abbr-list pt5" ng-if="ou.referrer != ''">
								<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','From');?>">&#xE8A0;</i><a target="_blank" rel="noopener" href="http:{{ou.referrer}}" title="{{ou.referrer}}">{{ou.referrer}}</a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div ng-if="online.onlineusers.length == 0" class="m10 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>
	</div>
</div>
