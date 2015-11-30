<?php $attribute = 'front_tabs'; 
$configExplain = erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Supported: dashboard,online_map,online_users,pending_chats,online_map,active_chats,unread_chats,closed_chats,online_operators');?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>