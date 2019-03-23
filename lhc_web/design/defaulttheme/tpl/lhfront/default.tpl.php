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

    <div ng-controller="OnlineCtrl as online" ng-init='online.forbiddenVisitors=<?php $currentUser->hasAccessTo('lhchat', 'use_onlineusers') != true ? print 'true' : print 'false'?>;groupByField = <?php echo json_encode($ogroupBy) ?>;online.maxRows="<?php echo (int)$omaxRows ?>";online.time_on_site = <?php echo json_encode($oTimeOnSite)?>;online.country="<?php echo htmlspecialchars($oCountry)?>";online.updateTimeout="<?php echo (int)$oupdTimeout ?>";online.userTimeout = "<?php echo (int)$ouserTimeout ?>";online.department="<?php echo (int)$onlineDepartment ?>";online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false' ?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false' ?>'>

        <div role="tabpanel" id="tabs" ng-cloak>
            <ul class="nav nav-pills" role="tablist">

                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list_tab.tpl.php')); ?>

                <?php foreach ($frontTabsOrder as $frontTab) : ?>
                    <?php if (trim($frontTab) == 'online_users' && $online_visitors_enabled_pre == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users_tab.tpl.php')); ?>
                    <?php elseif (trim($frontTab) == 'online_map' && $online_visitors_enabled_pre == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online_tab.tpl.php')); ?>
                    <?php elseif (trim($frontTab) == 'dashboard') : ?>
                        <?php if ($online_chat_enabled_pre == true) : ?>
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_dashboard_tab.tpl.php')); ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_custom_list_tab_multiinclude.tpl.php')); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" ng-cloak>

                <div role="tabpanel" class="tab-pane" id="chatdashboard">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list.tpl.php')); ?>
                </div>

                <?php foreach ($frontTabsOrder as $frontTab) : ?>
                    <?php if (trim($frontTab) == 'online_users' && $online_visitors_enabled_pre == true) : ?>
                        <div role="tabpanel" class="tab-pane" id="onlineusers">
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users.tpl.php')); ?>
                        </div>
                    <?php elseif (trim($frontTab) == 'online_map' && $online_visitors_enabled_pre == true) : ?>
                        <div role="tabpanel" class="tab-pane" id="map">
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online.tpl.php')); ?>
                        </div>
                    <?php elseif (trim($frontTab) == 'dashboard' && $online_chat_enabled_pre == true) : ?>
                        <div role="tabpanel" class="tab-pane" id="dashboard">
                            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/index.tpl.php')); ?>
                        </div>

                    <?php else : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_custom_list_multiinclude.tpl.php')); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            lhinst.attachTabNavigator();
            $('#right-column-page').removeAttr('id');
            $('#tabs a:first').tab('show')
        });
    </script>
<?php else : ?>

    <?php include(erLhcoreClassDesign::designtpl('lhfront/default_if_no_module.tpl.php')); ?>

<?php endif; ?>