<div class="panel panel-default panel-dashboard">
	<div class="panel-heading">
		<a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><i class="icon-chat chat-pending"></i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/pending_chats.tpl.php'));?> ({{pending_chats.list.length}}{{pending_chats.list.length == lhc.limitp ? '+' : ''}})</a>
	</div>
	<div>  
         
          <?php $optinsPanel = array('panelid' => 'pendingd','limitid' => 'limitp'); ?>
	      <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
			
          <div class="panel-list" ng-if="pending_chats.list.length > 0">
			<table class="table table-condensed mb0 table-small table-fixed">
				<thead>
					<tr>
						<th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor')?>" class="icon-user"></i></th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time')?>" class="icon-clock"></i></th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department')?>" class="icon-home"></i></th>
					</tr>
				</thead>
				<tr ng-repeat="chat in pending_chats.list track by chat.id">
					<td>
						<div data-chat-id="{{chat.id}}" data-toggle="popover" data-placement="top" class="abbr-list" ><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="icon-popup" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)"></a> <span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" /></span> <a class="icon-reply" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Redirect user to contact form.');?>" ng-click="lhc.redirectContact(chat.id,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Are you sure?');?>')"></a> <a ng-click="lhc.previewChat(chat.id)" class="icon-info"></a> <a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"> {{chat.nick}} </a>
							
							<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat')?>" class="icon-cancel-squared pull-right" ng-click="lhc.deleteChat(chat.id)"></a>
						</div>
						
						<div id="popover-title-{{chat.id}}" class="hide">
						  {{chat.nick}} [{{chat.id}}]
						</div>
						
						<div id="popover-content-{{chat.id}}" class="hide">
						    {{chat.time_created_front}}<br/>
							<i class='icon-user'></i> {{chat.plain_user_name ? chat.plain_user_name : '-'}}<br />
							<i class='icon-home'></i> {{chat.department_name}}
						</div>
						
					</td>
					<td>
						<div class="abbr-list" title="{{chat.wait_time_pending}}">{{chat.wait_time_pending}}</div>
					</td>
					<td>
						<div class="abbr-list" title="{{chat.department_name}}">{{chat.department_name}}</div>
					</td>
				</tr>
			</table>
		</div>
		
		<div ng-if="pending_chats.list.length == 0" class="m10 alert alert-info"><i class="icon-search"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>
		
	</div>
</div>
