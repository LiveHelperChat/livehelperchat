<ul class="no-bullet small-list">
    <li ng-repeat="chat in transfer_dep_chats.list">
    	<img class="action-image right-action-hide" align="absmiddle" ng-click="lhc.startChatTransfer(chat.id,chat.nick,chat.transfer_id)" src="<?php echo erLhcoreClassDesign::design('images/icons/accept.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">
	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','singlechatwindow')) : ?>
      	<img class="action-image" align="absmiddle" ng-click="lhc.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id)" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">
	<?php endif; ?>
	{{chat.id}}. {{chat.nick}} ({{chat.time_front}})
    </li>
</ul>
<p ng-show="transfer_dep_chats.list.length == 0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
