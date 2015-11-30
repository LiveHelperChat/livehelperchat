<?php $attribute = 'dashboard_order';
$configExplain = erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Supported: online_operators, departments_stats, online_visitors, pending_chats, unread_chats, transfered_chats, active_chats, closed_chats');?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>