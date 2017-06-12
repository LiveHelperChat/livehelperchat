<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
	<div class="panel panel-default panel-dashboard" data-panel-id="my_chats" ng-init="lhc.getToggleWidget('my_chats_widget_exp')">
		<div class="panel-heading">
			<a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_id)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons chat-active">account_box</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/my_active_chats.tpl.php'));?> ({{active_chats.list.length}}{{my_chats.list.length == lhc.limitmc ? '+' : ''}})</a>
			<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('my_chats_widget_exp')" class="fs24 pull-right material-icons exp-cntr">{{lhc.toggleWidgetData['my_chats_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
		</div>

		<div ng-if="lhc.toggleWidgetData['my_chats_widget_exp'] !== true">

			<?php $optinsPanel = array('panelid' => 'mcd','limitid' => 'limitmc'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

			<div ng-if="my_chats.list.length > 0" class="panel-list">
				<table class="table table-condensed mb0 table-small table-fixed">
					<thead>
						<tr>
							<th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i></th>
							<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Status');?>" class="material-icons">&#xE80E;</i></th>
							<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
						</tr>
					</thead>
					<tr ng-repeat="chat in my_chats.list track by chat.id" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
						<td>

							<div data-toggle="popover" data-placement="top" data-chat-id="{{chat.id}}" class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">open_in_new</a><a ng-click="lhc.previewChat(chat.id)" class="material-icons">info_outline</a> <a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"> {{chat.nick}} </a></div>

							<div id="popover-title-{{chat.id}}" class="hide">
							  {{chat.nick}} [{{chat.id}}]
							</div>

							<div id="popover-content-{{chat.id}}" class="hide">
								<i class="material-icons">access_time</i>{{chat.time_created_front}}<br />
								<i class="material-icons">account_box</i>{{chat.plain_user_name}}<br />
								<i class="material-icons">home</i>{{chat.department_name}}<br />
								<span ng-show="chat.product_name"><i class="material-icons">&#xE8CC;</i>{{chat.product_name}}</span>
							</div>

						</td>
						<td>						  
						
							<div class="abbr-list" title="{{chat.status == 1 ? 'Active' : 'Pending'}}">
							    <i ng-if="chat.status != 1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Pending chat');?>" class="material-icons chat-unread">&#xE80E;</i>
                                {{chat.wait_time_pending}}
							</div>							
						</td>
						<td>
							<div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
						</td>
					</tr>
				</table>
			</div>
			
			<div ng-if="!my_chats || my_chats.list.length == 0" class="m10 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>

		</div>
	</div>
<?php endif; ?>