<div class="row">
    <div class="col chats-column border-right pr-0 pl-0">
        <nav class="navbar navbar-expand-lg navbar-light bg-white p-0 pb-1 pt-1 border-bottom home-cog-dashboard-settings" style="z-index: 2">
            <button class="navbar-toggler pl-2 pr-2 pt-1 pb-1 m-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon nav-bar-toggler-sm"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav w-100">
                    <?php if (isset($frontTabsOrder)) : foreach ($frontTabsOrder as $frontTab) : ?>
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
                    <?php endforeach; endif;?>

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/dashboard_my_chats_options.tpl.php'));?>
                </ul>
            </div>
        </nav>

        <div class="home-dashboard-left-column">

            <div class="overflow-auto">
                <div class="chat-list-row d-flex" ng-repeat="chat in my_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
                    <div class="col-12 col-sm-3 col-lg-2 align-self-center pl-1 pr-1">

                            <svg viewBox="-2 -2 105 105" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <filter id="f1" x="0" y="0" width="200%" height="200%">
                                        <feOffset result="offOut" in="SourceAlpha" dx="2" dy="2" />
                                        <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
                                        <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                                    </filter>
                                </defs>
                                <circle cx="50" cy="50" r="49" stroke="{{lhc.chatMetaData[chat.id]['um'] == 1 ? '#BB474A' : '#5da423'}}" stroke-width="3" fill="{{lhc.chatMetaData[chat.id]['cbg']}}" />
                                <circle filter="url(#f1)" cx="70" cy="75" r="12" fill="{{lhc.chatMetaData[chat.id]['ucs'] == 0 ? '#5da423' : (lhc.chatMetaData[chat.id]['ucs'] == 2 ? '#EDC21A' : '#BB474A')}}" />
                                <text x="48%" y="48%" text-anchor="middle" stroke="white" stroke-width="2px" style="text-shadow: 1px 1px 2px black;font-size:26px;" dy=".3em">{{lhc.chatMetaData[chat.id]['ctit']}} {{lhc.chatMetaData[chat.id]['mn'] > 0 ? '(' + lhc.chatMetaData[chat.id]['mn'] + ')' : ''}}</text>
                            </svg>

                    </div>
                    <div class="col-8 col-sm-9 col-lg-10 d-none d-sm-block pr-0 pl-0">

                        <span class="float-right fs11 d-none d-md-inline pr-1">
                            <span title="Wait time" ng-if="!chat.status">{{chat.wait_time_pending}}</span>
                            <span title="{{chat.time_created_front}}" ng-if="chat.status == 1 || chat.status == 5 || chat.status == 4">
                                {{lhc.getCreateTime(chat.last_action_ago)}}
                            </span>
                            <span title="Close time" ng-if="chat.status == 2" title="Close time - {{chat.cls_time_front}}">{{lhc.getCreateTime(chat.last_action_ago)}}</span>
                        </span>

                        <div class="fs13">
                                    <span ng-if="chat.country_code != undefined">
                                        <img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;
                                    </span>
                                    <span title="{{chat.id}}" class="fs12" ng-class="{'chat-pending font-weight-bold': !chat.status, 'chat-unread': chat.status == 2 || lhc.chatMetaData[chat.id]['mn'] > 0}">{{lhc.truncateScope(chat.nick,10)}}</span>
                        </div>

                        <div class="fs11 pt-1 text-secondary">
                            <i title="Department" class="material-icons">home</i>{{chat.department_name}}
                            <span class="d-none d-md-inline"><i title="Operator" class="material-icons">account_box</i>{{chat.plain_user_name}}</span>
                        </div>

                        <p class="pb-1 pl-1 mb-0 fs12 text-secondary text-truncate">{{lhc.chatMetaData[chat.id]['lmsg']}}</p>
                    </div>
                </div>
            </div>

            <div class="chat-list-row d-flex" ng-repeat="chat in my_open_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">

            <div class="col-12 col-sm-3 col-lg-2 align-self-center pl-1 pr-1">
                <svg viewBox="-2 -2 105 105" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <filter id="f1" x="0" y="0" width="200%" height="200%">
                            <feOffset result="offOut" in="SourceAlpha" dx="2" dy="2" />
                            <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
                            <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                        </filter>
                    </defs>
                    <circle cx="50" cy="50" r="49" stroke="{{lhc.chatMetaData[chat.id]['um'] == 1 ? '#BB474A' : '#5da423'}}" stroke-width="3" fill="{{lhc.chatMetaData[chat.id]['cbg']}}" />
                    <circle filter="url(#f1)" cx="70" cy="75" r="12" fill="{{lhc.chatMetaData[chat.id]['ucs'] == 0 ? '#5da423' : (lhc.chatMetaData[chat.id]['ucs'] == 2 ? '#EDC21A' : '#BB474A')}}" />
                    <text x="48%" y="48%" text-anchor="middle" stroke="white" stroke-width="2px" style="text-shadow: 1px 1px 2px black;font-size:26px;" dy=".3em">{{lhc.chatMetaData[chat.id]['ctit']}} {{lhc.chatMetaData[chat.id]['mn'] > 0 ? '(' + lhc.chatMetaData[chat.id]['mn'] + ')' : ''}}</text>
                </svg>
            </div>
            <div class="col-8 col-sm-9 col-lg-10 d-none d-sm-block pr-0 pl-0">

                <span class="float-right fs11">
                    <span ng-click="lhc.closeExternalChat(chat); $event.stopPropagation();" title="Stop monitoring, chat status won't be changed" class="material-icons text-danger font-weight-bold mr-0">close</span>
                </span>

                <span class="float-right fs11 d-none d-md-inline pr-1">
                        <span title="Wait time" ng-if="!chat.status">{{chat.wait_time_pending}}</span>
                        <span title="{{chat.time_created_front}}" ng-if="chat.status == 1 || chat.status == 5 || chat.status == 4">
                            {{lhc.getCreateTime(chat.last_action_ago)}}
                        </span>
                        <span title="Close time" ng-if="chat.status == 2" title="Close time - {{chat.cls_time_front}}">{{lhc.getCreateTime(chat.last_action_ago)}}</span>
                    </span>

                <div class="fs13">

                    <span ng-if="chat.country_code != undefined">
                        <img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;
                    </span>

                    <span title="{{chat.id}}" class="fs12" ng-class="{'chat-pending font-weight-bold': !chat.status, 'chat-unread': chat.status == 2,'font-weight-bold' : lhc.chatMetaData[chat.id]['mn'] > 0}">{{lhc.truncateScope(chat.nick,10)}}</span>

                    <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm">({{lhc.chatMetaData[chat.id]['mn']}})</span>
                </div>

                <div class="fs11 pt-1 text-secondary ">
                    <i title="Department" class="material-icons">home</i>{{chat.department_name}}
                    <span class="d-none d-md-inline"><i title="Operator" class="material-icons">account_box</i>{{chat.plain_user_name}}</span>
                </div>
                <p class="pb-1 pl-1 mb-0 fs12 text-secondary text-truncate">{{lhc.chatMetaData[chat.id]['lmsg']}}</p>
            </div>
        </div>
    </div>
    </div>

    <div class="col overflow-auto">

        <div ng-repeat="chat in lhc.syncChatsOpen" ng-show="chat == lhc.current_chat_id" class="chat-content-dashboard" id="chat-content-{{chat}}"></div>

        <?php if (isset($frontTabsOrder)) : foreach ($frontTabsOrder as $frontTab) : ?>
            <?php if (trim($frontTab) == 'online_users' && $online_visitors_enabled_pre == true) : ?>
                <div ng-show="lhc.currentPanel == 'onlineusers'" class="pt-1">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users.tpl.php')); ?>
                </div>
            <?php elseif (trim($frontTab) == 'online_map' && $online_visitors_enabled_pre == true) : ?>
                <div ng-show="lhc.currentPanel == 'map'" class="pt-1">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online.tpl.php')); ?>
                </div>
            <?php elseif (trim($frontTab) == 'dashboard' && $online_chat_enabled_pre == true) : ?>
                <div ng-show="lhc.currentPanel == 'dashboard'" class="pt-1">
                    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/index.tpl.php')); ?>
                </div>
            <?php else : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_custom_list_multiinclude.tpl.php')); ?>
            <?php endif; ?>
        <?php endforeach; endif; ?>

        <?php if (isset($showChatsLists) && $showChatsLists == true) : ?>
            <?php if ($pendingTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'pending-chats'" class="pt-1 pb-1">
                    <div id="pending-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
                </div>
            <?php endif;?>

            <?php if ($activeTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'active-chats'" class="pt-1 pb-1">
                    <div id="active-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
                </div>
            <?php endif;?>

            <?php if ($unreadTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'unread-chats'" class="pt-1 pb-1">
                    <div id="unread-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
                </div>
            <?php endif;?>

            <?php if ($closedTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'closed-chats'" class="pt-1 pb-1">
                    <div id="closed-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
                </div>
            <?php endif;?>
        <?php endif;?>

    </div>
</div>