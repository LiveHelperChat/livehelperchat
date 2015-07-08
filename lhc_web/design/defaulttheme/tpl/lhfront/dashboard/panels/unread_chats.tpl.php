<div class="panel panel-default panel-dashboard">
	<div class="panel-heading">
		<a href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><i class="icon-comment chat-unread"></i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/unread_chats.tpl.php'));?> ({{unread_chats.list.length}}{{unread_chats.list.length == lhc.limitu ? '+' : ''}})</a>
	</div>
	<div>
					
		<?php $optinsPanel = array('panelid' => 'unreadd','limitid' => 'limitu'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
					
		<div class="panel-list" ng-if="unread_chats.list.length > 0">
			<table class="table table-condensed mb0 table-small table-fixed">
				<thead>
					<tr>
						<th width="50%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="icon-user"></i></th>
						<th width="30%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Time ago');?>" class="icon-clock"></i></th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="icon-home"></i></th>
					</tr>
				</thead>
				<tr ng-repeat="chat in unread_chats.list track by chat.id">
					<td>
						<div data-chat-id="{{chat.id}}" data-toggle="popover" data-placement="top" class="abbr-list" ><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="icon-popup" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)"></a> <span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" /></span> <a ng-click="lhc.previewChat(chat.id)" class="icon-info"></a> <a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"> {{chat.nick}} </a></div>
						
						<div id="popover-title-{{chat.id}}" class="hide">
						  {{chat.nick}} [{{chat.id}}]
						</div>
						
						<div id="popover-content-{{chat.id}}" class="hide">
						    {{chat.time_created_front}}<br/>
							<i class='icon-user'></i> {{chat.plain_user_name}}<br />
							<i class='icon-home'></i> {{chat.department_name}}
						</div>
						
					</td>
					<td>
						<div class="abbr-list" title="{{chat.unread_time.hours}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> {{chat.unread_time.minits}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> {{chat.unread_time.seconds}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.">{{chat.unread_time.hours}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> {{chat.unread_time.minits}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> {{chat.unread_time.seconds}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</div>
					</td>
					<td>
						<div class="abbr-list" title="{{chat.department_name}}">{{chat.department_name}}</div>
					</td>
				</tr>
			</table>
		</div>

		<div ng-if="unread_chats.list.length == 0" class="m10 alert alert-info">
			<i class="icon-search"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>

	</div>
</div>
