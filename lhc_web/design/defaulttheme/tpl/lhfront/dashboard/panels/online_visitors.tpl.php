<div class="card card-dashboard card-visitors" data-panel-id="online_visitors" id="widget-onvisitors" ng-class="lhc.toggleWidgetData['onvisitors_widget_exp'] !== true ? 'active' : ''" ng-init="lhc.getToggleWidget('onvisitors_widget_exp')">
	<div class="card-header">
        <i class="material-icons action-image" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/sendmassmessage'})">send</i><i class="material-icons">face</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/online_visitors.tpl.php'));?> ({{online.onlineusers.length}})

		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('onvisitors_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['onvisitors_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

        <?php $takenTimeAttributes = 'online.onlineusers_tt';?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
	</div>
	<div ng-if="lhc.toggleWidgetData['onvisitors_widget_exp'] !== true" id="widget-onvisitors-body">

        <div class="p-2">
           <div class="row">
               <div class="col-3 pe-0">
                    <input class="form-control form-control-sm" ng-model="query" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Type to search')?>">
               </div>
                <div class="col-3 pe-2">
                    <?php $optinsPanel = array(
                        'panelid' => 'department',
                        'limitid' => 'limitod',
                        'hide_limits' => true,
                        'padding_filters' => 0,
                        'disable_product' => true,
                        'no_names_department' => true,
                        'hide_department_variations' => true,
                        'controller_panel' => 'online'
                    ); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
                </div>
               <?php $columnCountrySize = 3?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/country_filter.tpl.php')); ?>

               <?php $columnCountrySize = 3;?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/time_on_site_filter.tpl.php')); ?>
           </div>
        </div>

	   <div class="panel-list" ng-if="online.onlineusers.length > 0">
			<table ng-cloak class="table table-sm mb-0 table-small table-fixed" ng-class="{'filter-online-active' : online.online_connected}"  cellpadding="0" cellspacing="0" ng-init='trans = <?php echo json_encode(array('third' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has not seen a message from the operator, or the message window is still open.'),'msg_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Seen'),'msg_not_seen' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Unseen'),'second' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.'),'first' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator')),JSON_HEX_APOS)?>'>
				<thead>
                    <th width="50%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Main information')?>
                        <a href="#" ng-click="online.showConnected()" class="text-muted">
                            <i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Show only connected');?>">{{online.online_connected ? 'flash_on' : 'flash_off'}}</i>
                        </a>
                    </th>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header_online.tpl.php'));?>
                </thead>
                <tbody ng-repeat="group in onlineusersGrouped track by group.id">

					<tr ng-show="group.label != ''">
						<td><h5 class="group-by-{{groupByField}}">{{group.label}} ({{group.ou.length}})</h5></td>
      				</tr>
					<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id" id="uo-vid-{{ou.vid}}" class="online-user-filter-row" ng-class="{<?php echo $onlineCheck?>}">
						<td nowrap="nowrap">
							<div class="btn-group" role="group" aria-label="...">
                                <a href="#" class="btn btn-xs btn-outline-secondary" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Copy nick');?>" onclick="lhinst.copyContent($(this))" data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Copied');?>" data-copy="{{ou.nick}}"><i class="material-icons me-0">content_copy</i></a>
								<a href="#" class="btn btn-xs btn-outline-secondary" id="ou-face-{{ou.vid}}" <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/face_icon.tpl.php'));?> ng-click="online.showOnlineUserInfo(ou.id)"><i class="material-icons">info_outline</i>{{ou.lastactivity_ago}} | {{ou.nick}}&nbsp;<img ng-if="ou.user_country_code" ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" /></a><?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/custom_online_button_multiinclude.tpl.php')); ?><span ng-click="online.previewChat(ou)" class="btn btn-xs btn-outline-success action-image" ng-show="ou.chat_id > 0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Preview chat')?>"><i class="material-icons me-0">chat</i></span><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visits');?> ({{ou.total_visits}})" class="btn btn-xs btn-outline-info" ng-show="ou.total_visits > 1"><i class="material-icons">face</i>({{ou.total_visits}})</span><span class="btn btn-outline-success btn-xs" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New');?>" ng-show="ou.total_visits == 1"><i class="material-icons me-0">face</i></span>
                                <span title="{{ou.message_seen == 1 ? trans.msg_seen : trans.msg_not_seen}} | {{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" class="btn btn-xs" ng-show="ou.operator_message" ng-class="ou.message_seen == 1 ? 'btn-outline-success' : 'btn-outline-danger'"><i class="material-icons me-0">chat_bubble_outline</i></span>
                                <a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Start a chat');?>" class="btn btn-xs btn-outline-secondary" ng-click="lhc.openModal('chat/sendnotice/'+ou.id);"><i class="material-icons me-0">send</i></a>
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
		<div ng-if="online.onlineusers.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','You will see short list of your site visitors here.')?>...</div>
	</div>
</div>
