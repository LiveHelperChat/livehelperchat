<?php
$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$closedTabEnabled = 0;//erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
$showChatsLists = true;
?>

<div ng-cloak id="tabs">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list.tpl.php')); ?>
</div>