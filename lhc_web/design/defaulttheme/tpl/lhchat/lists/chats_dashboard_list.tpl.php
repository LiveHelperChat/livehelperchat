<div class="row">
    <div class="col chats-column border-right pr-0 pl-2">

        <?php if (isset($showChatsLists) && $showChatsLists == true) :?><div class="cog-dashboard-settings bg-light rounded border"><?php endif; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/dashboard_my_chats_options.tpl.php'));?>
        <?php if (isset($showChatsLists) && $showChatsLists == true) :?></div><?php endif; ?>

        <div class="<?php isset($showChatsLists) && $showChatsLists == true ? print 'dashboard-left-column' : print ''?>">
            <h6 class="border-bottom pb-1 mb-0 pt-1 clearfix"><span class="text-secondary float-left fs13 pr-2">[{{my_chats.list.length}}]</span> <span class="d-none d-sm-block text-truncate">My chats</span></h6>
            <div class="overflow-auto">
                <div class="chat-list-row border-bottom d-flex" ng-repeat="chat in my_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
                    <div  class="col-12 col-sm-3 col-lg-2 align-self-center p-1">

                        <div class="clearfix pb-1">
                            <i id="msg-send-status-{{chat.id}}" ng-class="{'icon-user-offline' : lhc.chatMetaData[chat.id]['um'] == 1}" title="Last message send status" class="pt-1 mr-0 fs12 material-icons icon-user-online float-left">send</i>
                            <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm pl-1 float-left d-inline d-sm-none fs11">({{lhc.chatMetaData[chat.id]['mn']}})</span>
                            <i id="user-chat-status-{{chat.id}}" ng-class="{'icon-user-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-user-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-user-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}" class="icon-user-status mr-0 material-icons float-right">face</i>
                        </div>

                        <img id="chat-icon-img-{{chat.id}}" ng-class="{'icon-svg-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-svg-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-svg-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}"  class="img-fluid w-100 align-self-center rounded icon-svg-status" src="<?php echo erLhcoreClassDesign::design('images/general/logo.png')?>" />
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

                                    <span title="{{chat.id}}" class="fs12" ng-class="{'chat-pending font-weight-bold': !chat.status, 'chat-unread': chat.status == 2,'font-weight-bold' : lhc.chatMetaData[chat.id]['mn'] > 0}">{{lhc.truncateScope(chat.nick,10)}}</span>

                                    <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm">({{lhc.chatMetaData[chat.id]['mn']}})</span>
                        </div>

                        <div class="fs11 pt-1 text-secondary">
                            <i title="Department" class="material-icons">home</i>{{chat.department_name}}
                            <span class="d-none d-md-inline"><i title="Operator" class="material-icons">account_box</i>{{chat.plain_user_name}}</span>
                        </div>

                        <p class="pb-1 pl-1 mb-0 fs12 text-secondary text-truncate">{{lhc.chatMetaData[chat.id]['lmsg']}}</p>
                    </div>
                </div>
            </div>

            <h6 class="border-bottom pb-2 pt-2 mt-2 mb-0 clearfix"><span class="text-secondary float-left fs13 pr-2">[{{my_open_chats.list.length}}]</span> <span class="d-none d-sm-block text-truncate">Monitored chats</span></h6>
            <div class="chat-list-row border-bottom d-flex" ng-repeat="chat in my_open_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">

            <div class="col-12 col-sm-3 col-lg-2 align-self-center p-1">
                <div class="clearfix pb-1">
                    <i id="msg-send-status-{{chat.id}}" ng-class="{'icon-user-offline' : lhc.chatMetaData[chat.id]['um'] == 1}" title="Last message send status" class="pt-1 mr-0 fs12 material-icons icon-user-online float-left">send</i>

                    <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm pl-1 float-left d-inline d-sm-none fs11">({{lhc.chatMetaData[chat.id]['mn']}})</span>

                    <i id="user-chat-status-{{chat.id}}" ng-class="{'icon-user-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-user-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-user-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}" class="icon-user-status mr-0 material-icons float-right">face</i>
                </div>
                <img id="chat-icon-img-{{chat.id}}" ng-class="{'icon-svg-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-svg-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-svg-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}"  class = "img-fluid w-100 align-self-center rounded  icon-svg-status" src="<?php echo erLhcoreClassDesign::design('images/general/logo.png')?>"/>
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
    <div class="col">

        <div ng-repeat="chat in lhc.syncChatsOpen" ng-show="chat == lhc.current_chat_id" id="chat-content-{{chat}}"></div>

        <?php if ($showChatsLists == true) : ?>
            <?php if ($pendingTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'pending-chats'">
                    <div id="pending-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
                </div>
            <?php endif;?>

            <?php if ($activeTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'active-chats'">
                    <div id="active-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
                </div>
            <?php endif;?>

            <?php if ($unreadTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'unread-chats'">
                    <div id="unread-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
                </div>
            <?php endif;?>

            <?php if ($closedTabEnabled == true) : ?>
                <div ng-show="lhc.currentPanel == 'closed-chats'">
                    <div id="closed-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?></div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
                </div>
            <?php endif;?>
        <?php endif;?>

    </div>
</div>