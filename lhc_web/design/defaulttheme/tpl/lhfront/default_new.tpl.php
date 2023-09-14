<?php include(erLhcoreClassDesign::designtpl('lhfront/online_chat_enabled_pre.tpl.php')); ?>

<?php if ($online_chat_enabled_pre == true || $online_visitors_enabled_pre == true) : ?>

    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/options/dashboard_main_options.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/options/left_list_options.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_general.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_online_check.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/tabs_multiinclude.tpl.php')); ?>

    <div ng-controller="OnlineCtrl as online" ng-init='online.forbiddenVisitors=<?php ($currentUser->hasAccessTo('lhchat', 'use_onlineusers') != true || (!in_array('online_users',$frontTabsOrder) && !in_array('online_visitors',$widgetsUser))) ? print 'true' : print 'false'?>;groupByField = <?php echo json_encode($ogroupBy) ?>;online.maxRows="<?php echo (int)$omaxRows ?>";online.time_on_site = <?php echo json_encode($oTimeOnSite, JSON_HEX_APOS)?>;online.country=<?php echo json_encode($oCountry,JSON_HEX_APOS)?>;online.updateTimeout="<?php echo (int)$oupdTimeout ?>";online.userTimeout = "<?php echo (int)$ouserTimeout ?>";online.department_dpgroups = <?php echo json_encode($onlineDepartmentGroups, JSON_HEX_APOS)?>;online.department=<?php echo json_encode($onlineDepartment, JSON_HEX_APOS)?>;online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false' ?>;online.online_connected=<?php echo $onlineVisitorOnly == 1 ? 'true' : 'false' ?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false' ?>'>
        <div class="row">
            <?php //include(erLhcoreClassDesign::designtpl('lhfront/dashboard/home_page_left.tpl.php'));?>
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
                    <div class="tab-content<?php (int)erLhcoreClassModelUserSetting::getSetting('column_chats',0) == 1 ? print ' tabs-column' : ''?><?php (int)erLhcoreClassModelUserSetting::getSetting('hide_tabs',1) == 1 ? print ' mt-0' : ''?>" ng-cloak>
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

    <?php if (isset($load_chat_id) && is_numeric($load_chat_id)) : ?>
        <input type="hidden" id="load_chat_id" value="<?php echo htmlspecialchars($load_chat_id)?>" />
    <?php endif; ?>

    <script>
        $(document).ready(function () {
            $('#right-column-page').removeAttr('id');
            $('#tabs a:first').tab('show');
            $('#tabs').on('shown.bs.tab', function (event) {
                if ($(event.target).hasClass('chat-nav-item')){
                    $(this).addClass('chat-tab-selected');
                } else {
                    $(this).removeClass('chat-tab-selected');
                }
            });
            $('.dashboard-panels,#dashboard').on('click', '.btn-department-dropdown', function() {
                if ($(this).hasClass('show')) {
                    $(this).parent().find('.filter-text-input').focus();
                }
            });
        });
    </script>
<?php else : ?>

    <?php include(erLhcoreClassDesign::designtpl('lhfront/default_if_no_module.tpl.php')); ?>

<?php endif; ?>