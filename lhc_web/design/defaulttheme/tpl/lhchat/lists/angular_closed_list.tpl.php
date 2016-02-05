<ul class="list-unstyled">				
      <li ng-repeat="chat in closed_chats.list track by chat.id" ng-class="{'icon-user-away': chat.user_status_front == 2, 'icon-user-online': chat.user_status_front == 0}">
      	<span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" /></span>
      	<a class="material-icons" title="ID - {{chat.id}}" ng-click="lhc.previewChat(chat.id)" >info_outline</a>
	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','singlechatwindow')) : ?>
	<a class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">open_in_new</a>
	<?php endif; ?>
	<a ng-click="lhc.startChat(chat.id,chat.nick)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>">
	{{chat.nick}}, <small><i>{{chat.time_created_front}},</i></small> {{chat.department_name}}
	</a>
      </li>					
</ul>
<p ng-show="closed_chats.list.length == 0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
