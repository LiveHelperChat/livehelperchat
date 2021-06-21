<table class="table table-sm mb-0 table-small table-fixed list-chat-table">
	<thead>
		<tr>
			<th width="40%">
			<a ng-click="lhc.toggleWidgetSort('active_chats_sort','loc_dsc','loc_asc',true)">
			 <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Location');?>" class="material-icons">&#xE0C8;</i>
			 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['active_chats_sort'] != 'loc_asc' && lhc.toggleWidgetData['active_chats_sort'] != 'loc_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by location')?>" class="material-icons">{{lhc.toggleWidgetData['active_chats_sort'] == 'loc_dsc' || lhc.toggleWidgetData['active_chats_sort'] != 'loc_asc' ? 'trending_up' : 'trending_down'}}</i>
			</a>&nbsp;&nbsp;&nbsp;<a ng-click="lhc.toggleWidgetSort('active_chats_sort','u_dsc','u_asc',true)">
			 <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i>
			 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['active_chats_sort'] != 'u_asc' && lhc.toggleWidgetData['active_chats_sort'] != 'u_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by visitor nick')?>" class="material-icons">{{lhc.toggleWidgetData['active_chats_sort'] == 'u_dsc' || lhc.toggleWidgetData['active_chats_sort'] != 'u_asc' ? 'trending_up' : 'trending_down'}}</i>
			</a>
			</th>           
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
			<th width="20%">
    			<a ng-click="lhc.toggleWidgetSort('active_chats_sort','lmt_dsc','lmt_asc',true)">
    			 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['active_chats_sort'] != 'lmt_asc' && lhc.toggleWidgetData['active_chats_sort'] != 'lmt_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by last message time')?>" class="material-icons">{{lhc.toggleWidgetData['active_chats_sort'] == 'lmt_dsc' || lhc.toggleWidgetData['active_chats_sort'] != 'lmt_asc' ? 'trending_up' : 'trending_down'}}</i>
    			</a>
                <a ng-click="lhc.toggleWidgetSort('active_chats_sort','id_dsc','id_asc',true)">
    			 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['active_chats_sort'] != 'id_asc' && lhc.toggleWidgetData['active_chats_sort'] != 'id_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by chat start time')?>" class="material-icons">{{lhc.toggleWidgetData['active_chats_sort'] == 'id_dsc' || lhc.toggleWidgetData['active_chats_sort'] != 'id_asc' ? 'trending_up' : 'trending_down'}}</i>
    			</a>
			</th>
			<th width="20%">
				<a ng-click="lhc.toggleWidgetSort('active_chats_sort','op_dsc','op_asc',true)">
				 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['active_chats_sort'] != 'op_asc' && lhc.toggleWidgetData['active_chats_sort'] != 'op_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by operator')?>" class="material-icons">{{lhc.toggleWidgetData['active_chats_sort'] == 'op_dsc' || lhc.toggleWidgetData['active_chats_sort'] != 'op_asc' ? 'trending_up' : 'trending_down'}}</i>
				</a>
			</th>
			<th width="20%">							
				<a ng-click="lhc.toggleWidgetSort('active_chats_sort','dep_dsc','dep_asc',true)">
				 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['active_chats_sort'] != 'dep_asc' && lhc.toggleWidgetData['active_chats_sort'] != 'dep_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by department')?>" class="material-icons">{{lhc.toggleWidgetData['active_chats_sort'] == 'dep_dsc' || lhc.toggleWidgetData['active_chats_sort'] != 'dep_asc' ? 'trending_up' : 'trending_down'}}</i>
				</a>							
			</th>
		</tr>
	</thead>
	<tr ng-repeat="chat in active_chats.list track by chat.id" ng-click="lhc.startChat(chat.id,chat.nick)" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': !chat.user_status_front}">
		<td>
			<div class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="[{{chat.id}}] {{chat.time_created_front}}" ng-click="lhc.previewChat(chat.id, $event)" class="material-icons">info_outline</a> <i class="material-icons" title="Offline request" ng-show="chat.status_sub == 7">mail</i><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Has unread messages');?>" ng-if="chat.hum" class="material-icons text-danger">feedback</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/icon.tpl.php'));?>{{chat.nick}}</div>
		</td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
		<td>
		  <div class="abbr-list" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Chat started at');?> - {{chat.time_created_front}}">
              <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Receive or send indicator and time since it happened');?>" ng-class="{'text-danger' : chat.pnd_rsp}"}>{{chat.pnd_rsp === true ? 'call_received' : 'call_made'}}</span>
              {{chat.last_msg_time_front ? chat.last_msg_time_front : '&#x2709;'}}
          </div>
		</td>
		<td>
			<div class="abbr-list" title="{{chat.n_off_full}} | {{chat.plain_user_name}}">{{chat.n_office}}</div>
		</td>
		<td>
			<div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
		</td>
	</tr>
</table>