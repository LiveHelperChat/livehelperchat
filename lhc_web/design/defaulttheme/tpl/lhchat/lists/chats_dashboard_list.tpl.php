<div class="row">
    <div class="col chats-column border-right pr-0 pl-2">

        <h6 class="border-bottom pb-2">My active pending chats</h6>

            <div class="chat-list-row pr-2 pl-2" ng-repeat="chat in my_chats.list track by chat.id" ng-click="lhc.startChatDashboard(chat.id,chat.last_msg_id)" ng-class="{'active-chat-row' : chat.id == lhc.current_chat_id,'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
                <div class="pt-1">
            <span class="float-right fs11">
                <i id="msg-send-status-{{chat.id}}" ng-class="{'icon-user-offline' : lhc.chatMetaData[chat.id]['um'] == 1}" title="Last message send status" class="material-icons icon-user-online">send</i>
                <span title="Wait time" ng-if="!chat.status">{{chat.wait_time_pending}}</span>
                <span title="Chat created" ng-if="chat.status == 1 || chat.status == 5 || chat.status == 4">{{chat.time_created_front}}</span>
                <span title="Close time" ng-if="chat.status == 2">{{chat.cls_time_front}}</span>
            </span>

                <div class="fs13">
                    <i class="material-icons" title="{{chat.id}}" ng-class="{'chat-pending': !chat.status, 'chat-active': chat.status == 1, 'chat-unread': chat.status == 2}">chat</i>

                    <i id="user-chat-status-{{chat.id}}" ng-class="{'icon-user-online' : lhc.chatMetaData[chat.id]['ucs'] == 0,'icon-user-away' : lhc.chatMetaData[chat.id]['ucs'] == 2,'icon-user-pageview' : lhc.chatMetaData[chat.id]['ucs'] == 3}" class="icon-user-status material-icons icon-user-online">face</i>
                    <span ng-if="chat.country_code != undefined">
                <img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;
            </span><span ng-class="{'font-weight-bold' : lhc.chatMetaData[chat.id]['mn'] > 0}">{{chat.nick}}</span>
                    <span ng-if="lhc.chatMetaData[chat.id]['mn'] > 0" class="msg-nm">({{lhc.chatMetaData[chat.id]['mn']}})</span>
                </div>
                <p class="pb-1 mb-0 fs12 text-secondary text-truncate">{{lhc.chatMetaData[chat.id]['lmsg']}}</p>
            </div>
        </div>


    </div>
    <div class="col">
        <div ng-repeat="chat in my_chats.list track by chat.id" ng-show="chat.id == lhc.current_chat_id" id="chat-content-{{chat.id}}">add sync</div>
    </div>
</div>