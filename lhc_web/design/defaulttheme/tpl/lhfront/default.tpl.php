<?php include(erLhcoreClassDesign::designtpl('lhfront/online_chat_enabled_pre.tpl.php')); ?>

<?php if ($online_chat_enabled_pre == true || $online_visitors_enabled_pre == true) : ?>

    <?php
    $canListOnlineUsers = false;
    $canListOnlineUsersAll = false;
    $currentUser = erLhcoreClassUser::instance();

    if (erLhcoreClassModelChatConfig::fetch('list_online_operators')->current_value == 1) {
        $canListOnlineUsers = $currentUser->hasAccessTo('lhuser', 'userlistonline');
        $canListOnlineUsersAll = $currentUser->hasAccessTo('lhuser', 'userlistonlineall');
    }

    $canseedepartmentstats = $currentUser->hasAccessTo('lhuser', 'canseedepartmentstats');

    $pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list', 1);
    $activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list', 1);

    $closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list', 0) && erLhcoreClassModelChatConfig::fetchCache('list_closed')->current_value == 1;

    $unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list', 0) && erLhcoreClassModelChatConfig::fetchCache('list_unread')->current_value == 1;

    $frontTabsOrder = explode(',', erLhcoreClassModelChatConfig::fetch('front_tabs')->current_value);
    ?>

    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_general.tpl.php')); ?>

    <div ng-controller="OnlineCtrl as online" ng-cloak id="tabs" class="h-100 chat-tabs-container" ng-init='online.forbiddenVisitors=<?php $currentUser->hasAccessTo('lhchat', 'use_onlineusers') != true ? print 'true' : print 'false'?>;groupByField = <?php echo json_encode($ogroupBy) ?>;online.maxRows="<?php echo (int)$omaxRows ?>";online.time_on_site = <?php echo json_encode($oTimeOnSite)?>;online.country="<?php echo htmlspecialchars($oCountry)?>";online.updateTimeout="<?php echo (int)$oupdTimeout ?>";online.userTimeout = "<?php echo (int)$ouserTimeout ?>";online.department="<?php echo (int)$onlineDepartment ?>";online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false' ?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false' ?>'>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list.tpl.php')); ?>
    </div>

    <script>
        $(document).ready(function () {
            $('#right-column-page').removeAttr('id');
        });
    </script>
<?php else : ?>

    <?php include(erLhcoreClassDesign::designtpl('lhfront/default_if_no_module.tpl.php')); ?>

<?php endif; ?>