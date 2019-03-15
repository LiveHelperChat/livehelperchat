<div class="row">
    <div class="col chats-column border-right pr-0 pl-2">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/dashboard_my_chats_options.tpl.php'));?>

        <h6 class="border-bottom pb-2 mb-0"><span class="text-secondary float-left fs13 pr-2">[{{my_chats.list.length}}]</span> <span class="text-truncate">My chats</span></h6>
        <div class="overflow-auto" style="max-height: 300px;">
            <div class="chat-list-row border-bottom row" ng-repeat="chat in my_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
                <div  class="col-2 align-self-center p-1 ml-1">
                    <i><img ng-class="{'icon-svg-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-svg-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-svg-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}"  class = "img-fluid w-100 align-self-center rounded  icon-svg-status" src="{{lhc.getIcon(chat.id)}}"/></i>
                </div>
                <div class="col-8">
                    <i id="msg-send-status-{{chat.id}}" ng-class="{'icon-user-offline' : lhc.chatMetaData[chat.id]['um'] == 1}" title="Last message send status" class="pt-1 fs12 material-icons icon-user-online float-right">send</i>

                    <span class="float-right fs11 d-none d-lg-inline pr-1">
                        <span title="Wait time" ng-if="!chat.status">{{chat.wait_time_pending}}</span>
                        <span title="{{chat.time_created_front}}" ng-if="chat.status == 1 || chat.status == 5 || chat.status == 4">
                            {{lhc.getCreateTime(chat.last_action_ago)}}
                        </span>
                        <span title="Close time" ng-if="chat.status == 2" title="Close time - {{chat.cls_time_front}}">{{lhc.getCreateTime(chat.last_action_ago)}}</span>
                    </span>


                    <div class="fs13">
                        <div  class="fs11 pt-2 text-secondary d-none d-lg-inline">
                                <i id="user-chat-status-{{chat.id}}" ng-class="{'icon-user-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-user-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-user-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}" class="icon-user-status material-icons">face</i>
                        </div>
                                <span ng-if="chat.country_code != undefined">
                                    <img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;
                                </span>

                                <span title="{{chat.id}}" class="fs12" ng-class="{'chat-pending font-weight-bold': !chat.status, 'chat-unread': chat.status == 2,'font-weight-bold' : lhc.chatMetaData[chat.id]['mn'] > 0}">{{lhc.truncateScope(chat.nick,10)}}</span>

                                <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm">({{lhc.chatMetaData[chat.id]['mn']}})</span>
                    </div>

                    <div class="fs11 pt-2 text-secondary d-none d-lg-inline">
                        <i title="Department" class="material-icons">home</i>{{chat.department_name}}&nbsp;<i title="Operator" class="material-icons">account_box</i>{{chat.plain_user_name}}
                    </div>
                    <p class="pb-1 pl-1 mb-0 fs12 text-secondary text-truncate">{{lhc.chatMetaData[chat.id]['lmsg']}}</p>
                </div>
            </div>

        </div>

        <h6 class="border-bottom pb-2 pt-2 mt-2 mb-0"><span class="text-secondary float-left fs13 pr-2">[{{my_open_chats.list.length}}]</span> My monitored chats</h6>
        <div class="chat-list-row border-bottom" ng-repeat="chat in my_open_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
            <div class="pt-1">

                <span class="float-right fs11">
                    <i id="msg-send-status-{{chat.id}}" ng-class="{'icon-user-offline' : lhc.chatMetaData[chat.id]['um'] == 1}" title="Last message send status" class="fs12 material-icons icon-user-online">send</i>
                    <span ng-click="lhc.closeExternalChat(chat); $event.stopPropagation();" title="Stop monitoring, chat status won't be changed" class="material-icons text-danger font-weight-bold mr-0">close</span>
                </span>

                <span class="float-right fs11 d-none d-lg-inline pr-1">
                        <span title="Wait time" ng-if="!chat.status">{{chat.wait_time_pending}}</span>
                        <span title="{{chat.time_created_front}}" ng-if="chat.status == 1 || chat.status == 5 || chat.status == 4">
                            {{lhc.getCreateTime(chat.last_action_ago)}}
                        </span>
                        <span title="Close time" ng-if="chat.status == 2" title="Close time - {{chat.cls_time_front}}">{{lhc.getCreateTime(chat.last_action_ago)}}</span>
                    </span>

                <div class="fs13">

                    <i id="user-chat-status-{{chat.id}}" ng-class="{'icon-user-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-user-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-user-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}" class="icon-user-status material-icons">face</i>
                    <span ng-if="chat.country_code != undefined">
                            <img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;
                        </span>

                    <span title="{{chat.id}}" class="fs12" ng-class="{'chat-pending font-weight-bold': !chat.status, 'chat-unread': chat.status == 2,'font-weight-bold' : lhc.chatMetaData[chat.id]['mn'] > 0}">{{lhc.truncateScope(chat.nick,10)}}</span>

                    <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm">({{lhc.chatMetaData[chat.id]['mn']}})</span>
                </div>

                <div class="fs11 pt-2 text-secondary d-none d-lg-inline">
                    <i title="Department" class="material-icons">home</i>{{chat.department_name}}&nbsp;<i title="Operator" class="material-icons">account_box</i>{{chat.plain_user_name}}
                </div>
                <p class="pb-1 pl-1 mb-0 fs12 text-secondary text-truncate">{{lhc.chatMetaData[chat.id]['lmsg']}}</p>
            </div>
        </div>






    </div>
    <div class="col">

        <div ng-repeat="chat in lhc.syncChatsOpen" ng-show="chat == lhc.current_chat_id" id="chat-content-{{chat}}"></div>

    </div>
</div>