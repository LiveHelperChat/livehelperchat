<ul class="list-unstyled">				
      <li ng-repeat="chat in active_chats.list track by chat.id" >
      	<span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" /></span>
      	<a class="icon-info" title="ID - {{chat.id}}, {{chat.user_name}}" ng-click="lhc.previewChat(chat.id)" ></a><a class="right-action-hide icon-chat" ng-click="lhc.startChat(chat.id,chat.nick)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Add chat');?>"></a><a class="icon-popup" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>"></a> {{chat.nick}}, {{chat.time_created_front}}, {{chat.department_name}}
      </li>					
</ul>
<p ng-show="active_chats.list.length == 0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>