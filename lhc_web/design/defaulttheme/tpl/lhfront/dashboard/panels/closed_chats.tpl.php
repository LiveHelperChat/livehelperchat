<div class="card card-dashboard" data-panel-id="closed_chats" ng-init="lhc.getToggleWidget('closedc_widget_exp');lhc.getToggleWidgetSort('closed_chats_sort')">
	<div class="card-header">
		<a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/2"><i class="material-icons chat-closed">chat</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/closed_chats.tpl.php'));?> ({{closed_chats.list.length}}{{closed_chats.list.length == lhc.limitc ? '+' : ''}})</a>
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('closedc_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['closedc_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
	</div>
	<div ng-if="lhc.toggleWidgetData['closedc_widget_exp'] !== true">           
          <?php $optinsPanel = array('panelid' => 'closedd','limitid' => 'limitc'); ?>
		  <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>           
           
          <div class="panel-list" ng-inf="closed_chats.list.length > 0">
			<table class="table table-sm mb-0 table-small table-fixed">
				<thead>
					<tr>
						<th width="40%">
                            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i>
                            <a ng-click="lhc.toggleWidgetSort('closed_chats_sort','id_dsc','id_asc',true)">
                                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['closed_chats_sort'] != 'id_asc' && lhc.toggleWidgetData['closed_chats_sort'] != 'id_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort')?>" class="material-icons">{{lhc.toggleWidgetData['closed_chats_sort'] == 'id_dsc' || lhc.toggleWidgetData['closed_chats_sort'] != 'id_asc' ? 'trending_up' : 'trending_down'}}</i>
                            </a>
                        </th>
                        <th width="20%">
                            <a ng-click="lhc.toggleWidgetSort('closed_chats_sort','cst_dsc','cst_asc',true)">
                                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['closed_chats_sort'] != 'cst_asc' && lhc.toggleWidgetData['closed_chats_sort'] != 'cst_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by close time')?>" class="material-icons">{{lhc.toggleWidgetData['closed_chats_sort'] == 'cst_dsc' || lhc.toggleWidgetData['closed_chats_sort'] != 'cst_asc' ? 'trending_up' : 'trending_down'}}</i>
                            </a>
                        </th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator');?>" class="material-icons">account_box</i></th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
					</tr>
				</thead>
				<tr ng-repeat="chat in closed_chats.list track by chat.id" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
					<td>
						<div class="abbr-list" ><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">open_in_new</a><a ng-click="lhc.previewChat(chat.id)" class="material-icons">info_outline</a><a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"> {{chat.nick}} </a></div>
					</td>

                    <td>
                        <div class="abbr-list" title="{{chat.cls_time_front}}">{{chat.cls_time_front}}</div>
                    </td>

					<td>
						<div class="abbr-list" title="{{chat.plain_user_name}}">{{chat.plain_user_name}}</div>
					</td>
                    
					<td>
						<div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
					</td>
				</tr>
			</table>
		</div>
		
		<div ng-if="closed_chats.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>
		
		
	</div>
</div>
