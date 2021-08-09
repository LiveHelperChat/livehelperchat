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

    $mchatsTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_mchats_list', 1);

    $frontTabsOrder = explode(',', str_replace(' ','',erLhcoreClassModelChatConfig::fetch('front_tabs')->current_value));

    $dashboardOrder = json_decode(erLhcoreClassModelUserSetting::getSetting('dwo',''),true);

    if ($dashboardOrder === null) {
        if ($dashboardOrder == '') {
            $dashboardOrder = json_decode(erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value,true);
        }
    }

    $widgetsUser = array();
    foreach ($dashboardOrder as $widgetsColumn) {
        foreach ($widgetsColumn as $widget) {
            $widgetsUser[] = $widget;
        }
    }

    ?>

    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_general.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_online_check.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/tabs_multiinclude.tpl.php')); ?>

    <div ng-controller="OnlineCtrl as online" ng-init='online.forbiddenVisitors=<?php ($currentUser->hasAccessTo('lhchat', 'use_onlineusers') != true || (!in_array('online_users',$frontTabsOrder) && !in_array('online_visitors',$widgetsUser))) ? print 'true' : print 'false'?>;groupByField = <?php echo json_encode($ogroupBy) ?>;online.maxRows="<?php echo (int)$omaxRows ?>";online.time_on_site = <?php echo json_encode($oTimeOnSite)?>;online.country="<?php echo htmlspecialchars($oCountry)?>";online.updateTimeout="<?php echo (int)$oupdTimeout ?>";online.userTimeout = "<?php echo (int)$ouserTimeout ?>";online.department="<?php echo (int)$onlineDepartment ?>";online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false' ?>;online.online_connected=<?php echo $onlineVisitorOnly == 1 ? 'true' : 'false' ?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false' ?>'>

        <div class="row">
            <div translate="no" class="col chats-column border-right pr-0 pl-0">

                <div class="w-100 d-flex flex-column flex-grow-1">
                    <div class="clearfix bg-light">
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/open_active_chat_tab.tpl.php')); ?>

                        <div class="text-muted p-2 float-left"><i class="material-icons mr-0">list</i><span class="fs13 font-weight-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open chats'); ?></span></div>
                        <a class="d-inline-block pt-2 pr-1 float-right text-secondary"  onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/dashboardwidgets'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Configure dashboard')?>"><i class="material-icons mr-0">&#xE871;</i></a>

                        <?php if (in_array('online_users',$frontTabsOrder)) : ?>
                        <a class="d-inline-block pt-2 pr-1 float-right text-secondary" onclick="$('#tabs a[href=\'#onlineusers\']').tab('show')"><i class="material-icons md-18">face</i></a>
                        <?php endif; ?>

                        <?php if (in_array('online_map',$frontTabsOrder)) : ?>
                        <a class="d-inline-block pt-2 pr-1 float-right text-secondary" onclick="$('#tabs a[href=\'#map\']').tab('show')"><i class="material-icons md-18">place</i></a>
                        <?php endif; ?>

                        <a class="d-inline-block pt-2 pr-1 float-right text-secondary" onclick="$('#tabs a[href=\'#dashboard\']').tab('show')"><i class="material-icons md-18">home</i></a>

                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/icons/icons_multiinclude.tpl.php')); ?>
                    </div>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/basic_chat_enabled.tpl.php'));?>

                    <div role="tabpanel" class="border-top">

                        <?php if ((int)erLhcoreClassModelUserSetting::getSetting('left_list',0) == 0) : ?>
                        <ul class="nav nav-underline nav-small nav-fill mb-0 pb-0 border-bottom" role="tablist" id="sub-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="nav-link active" href="#sub-tabs-open" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open chats'); ?>" aria-controls="sub-tabs-open" role="tab" data-toggle="tab" aria-selected="true">
                                    <i class="material-icons chat-active">question_answer</i>
                                </a>
                            </li>
                            <?php if ($basicChatEnabled == true) : ?>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" title="<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/my_chats.tpl.php'));?>" href="#sub-tabs-my-assigned" aria-controls="sub-tabs-my-assigned" role="tab" data-toggle="tab" aria-selected="true">
                                    <i class="material-icons chat-active">account_box</i><span class="text-muted fs11 font-weight-bold">({{my_chats.list.length}}{{my_chats.list.length == 10 ? '+' : ''}})</span>
                                </a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" href="#sub-tabs-pending" title="<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/pending_chats.tpl.php'));?>" aria-controls="sub-tabs-pending" role="tab" data-toggle="tab" aria-selected="true">
                                    <i class="material-icons chat-pending">chat</i><span class="text-muted fs11 font-weight-bold">({{pending_chats.list.length}}{{pending_chats.list.length == 10 ? '+' : ''}})</span>
                                </a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" href="#sub-tabs-active" title="<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/active_chats.tpl.php'));?>" aria-controls="sub-tabs-active" role="tab" data-toggle="tab" aria-selected="true">
                                    <i class="material-icons chat-active">chat</i><span class="text-muted fs11 font-weight-bold">({{active_chats.list.length}}{{active_chats.list.length == 10 ? '+' : ''}})</span>
                                </a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" href="#sub-tabs-bot" title="<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/bot_chats.tpl.php'));?>" aria-controls="sub-tabs-bot" role="tab" data-toggle="tab" aria-selected="true">
                                    <i class="material-icons chat-active">android</i><span class="text-muted fs11 font-weight-bold">({{bot_chats.list.length}}{{bot_chats.list.length == lhc.limitb ? '+' : ''}})</span>
                                </a>
                            </li>
                            <?php endif;?>
                        </ul>
                        <?php endif; ?>

                        <div class="tab-content sub-tabs-content">
                            <div role="tabpanel" class="tab-pane active" id="sub-tabs-open">
                                <div id="tabs-dashboard"></div>

                                <?php if ($currentUser->hasAccessTo('lhgroupchat','use')) : ?>
                                <div class="border-top border-bottom bg-light card-header">
                                    <div class="text-muted"><i class="material-icons">list</i><span class="fs13 font-weight-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Group chats')?></span>
                                        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('group_chat_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['group_chat_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
                                    </div>
                                </div>

                                <div ng-if="lhc.toggleWidgetData['group_chat_widget_exp'] !== true">
                                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/group_chats.tpl.php'));?>
                                </div>
                                <?php endif;?>

                            </div>
                            <?php if ($basicChatEnabled == true && (int)erLhcoreClassModelUserSetting::getSetting('left_list',0) == 0) : ?>
                            <div role="tabpanel" class="tab-pane" id="sub-tabs-my-assigned">
                                <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/my_chats_panel.tpl.php'));?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="sub-tabs-pending">
                                <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/pending_panel.tpl.php'));?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="sub-tabs-active">
                                <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/active_panel.tpl.php'));?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="sub-tabs-bot">
                                <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bot_chats.tpl.php'));?>
                                <?php unset($rightPanelMode); unset($hideCardHeader); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($basicChatEnabled == true && (int)erLhcoreClassModelUserSetting::getSetting('left_list',0) == 1) : ?>
                    <div class="dashboard-panels d-flex flex-column flex-grow-1" style="position:relative">
                        <?php $hideCard = true; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/right_panel_container.tpl.php'));?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <div class="col">

                <div role="tabpanel" id="tabs" ng-cloak class="<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' pt-0' : ''?>">
                    <ul translate="no" class="nav nav-pills<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' d-none' : ''?>" role="tablist">
                        <?php foreach ($frontTabsOrder as $frontTab) : ?>
                            <?php if (trim($frontTab) == 'online_users' && $online_visitors_enabled_pre == true) : ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users_tab.tpl.php')); ?>
                            <?php elseif (trim($frontTab) == 'online_map' && $online_visitors_enabled_pre == true) : ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online_tab.tpl.php')); ?>
                            <?php elseif (trim($frontTab) == 'pending_chats') : ?>
                                <?php if ($pendingTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list_tab.tpl.php')); ?>
                                <?php endif; ?>
                            <?php elseif (trim($frontTab) == 'active_chats') : ?>
                                <?php if ($activeTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list_tab.tpl.php')); ?>
                                <?php endif; ?>
                            <?php elseif (trim($frontTab) == 'unread_chats') : ?>
                                <?php if ($unreadTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list_tab.tpl.php')); ?>
                                <?php endif; ?>
                            <?php elseif (trim($frontTab) == 'closed_chats') : ?>
                                <?php if ($closedTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list_tab.tpl.php')); ?>
                                <?php endif; ?>
                            <?php elseif (trim($frontTab) == 'dashboard') : ?>
                                <?php if ($online_chat_enabled_pre == true) : ?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_dashboard_tab.tpl.php')); ?>
                                <?php endif; ?>
                            <?php elseif (trim($frontTab) == 'online_operators') : ?>
                                <?php if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) : ?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list_tab.tpl.php')); ?>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_custom_list_tab_multiinclude.tpl.php')); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="tab-content<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-0' : ''?>" ng-cloak>
                        <?php foreach ($frontTabsOrder as $frontTab) : ?>
                            <?php if (trim($frontTab) == 'online_users' && $online_visitors_enabled_pre == true) : ?>
                                <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="onlineusers">
                                    <div>
                                        <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users.tpl.php')); ?>
                                    </div>
                                </div>
                            <?php elseif (trim($frontTab) == 'online_map' && $online_visitors_enabled_pre == true) : ?>
                                <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="map">
                                    <div>
                                        <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online.tpl.php')); ?>
                                    </div>
                                </div>
                            <?php elseif (trim($frontTab) == 'pending_chats' && $online_chat_enabled_pre == true) : ?>
                                <?php if ($pendingTabEnabled == true) : ?>
                                    <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="pendingchats">
                                        <div id="pending-chat-list">
                                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php')); ?>
                                        </div>
                                        <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list') ?>/(chat_status_ids)/0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'All pending chats'); ?></a>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (trim($frontTab) == 'active_chats' && $online_chat_enabled_pre == true) : ?>

                                <?php if ($activeTabEnabled == true) : ?>
                                    <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="activechats">
                                        <div id="active-chat-list">
                                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php')); ?>
                                        </div>
                                        <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list') ?>/(chat_status_ids)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'All active chats'); ?></a>
                                    </div>
                                <?php endif; ?>

                            <?php elseif (trim($frontTab) == 'unread_chats' && $online_chat_enabled_pre == true) : ?>

                                <?php if ($unreadTabEnabled == true) : ?>
                                    <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="unreadchats">
                                        <div id="unread-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php')); ?></div>
                                        <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list') ?>/(hum)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'All unread chats'); ?></a>
                                    </div>
                                <?php endif; ?>

                            <?php elseif (trim($frontTab) == 'closed_chats' && $online_chat_enabled_pre == true) : ?>

                                <?php if ($closedTabEnabled == true) : ?>
                                    <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="closedchats">
                                        <div id="closed-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php')); ?></div>
                                        <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list') ?>/(chat_status_ids)/2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'All closed chats'); ?></a>
                                    </div>
                                <?php endif; ?>

                            <?php elseif (trim($frontTab) == 'dashboard' && $online_chat_enabled_pre == true) : ?>

                                <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="dashboard">
                                    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/index.tpl.php')); ?>
                                </div>

                            <?php elseif (trim($frontTab) == 'online_operators') : ?>

                                <?php if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) : ?>
                                    <div role="tabpanel" class="tab-pane form-group<?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-3' : ''?>" id="onlineoperators">
                                        <div id="online-operator-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list.tpl.php')); ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_custom_list_multiinclude.tpl.php')); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function () {
            //lhinst.attachTabNavigator();
            $('#right-column-page').removeAttr('id');
            $('#tabs a:first').tab('show')
        });
    </script>
<?php else : ?>

    <?php include(erLhcoreClassDesign::designtpl('lhfront/default_if_no_module.tpl.php')); ?>

<?php endif; ?>