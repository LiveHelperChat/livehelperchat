<table class="table table-condensed mb0 table-small table-fixed">
    <thead>
    <tr>
        <th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor')?>" class="material-icons">face</i><a ng-click="lhc.toggleWidget('pending_chats_sort',true)"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort')?>" class="material-icons">{{lhc.toggleWidgetData['pending_chats_sort'] == false ? 'trending_up' : 'trending_down'}}</i></a></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time')?>" class="material-icons">access_time</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
    </tr>
    </thead>
    <tr ng-repeat="chat in pending_chats.list track by chat.id" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
        <td>
            <div data-chat-id="{{chat.id}}" data-toggle="popover" data-placement="top" class="abbr-list" ><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Delete chat')?>" class="material-icons pull-right" ng-click="lhc.deleteChat(chat.id)">delete</a><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">open_in_new</a><a class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Redirect user to contact form.');?>" ng-click="lhc.redirectContact(chat.id,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Are you sure?');?>')">reply</a><a ng-click="lhc.previewChat(chat.id)" class="material-icons">info_outline</a><a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"><i class="material-icons" title="Offline request" ng-show="chat.status_sub == 7">mail</i>{{chat.nick}} </a><small>{{chat.plain_user_name !== undefined ? ' | ' + chat.plain_user_name : ''}}</small></div>

            <div id="popover-title-{{chat.id}}" class="hide">
                {{chat.nick}} [{{chat.id}}]
            </div>

            <div id="popover-content-{{chat.id}}" class="hide">
                <i class="material-icons">access_time</i>{{chat.time_created_front}}<br/>
                <i class="material-icons">account_box</i>{{chat.plain_user_name ? chat.plain_user_name : '-'}}<br />
                <i class="material-icons">home</i>{{chat.department_name}}<br />
                <span ng-show="chat.product_name"><i class="material-icons">&#xE8CC;</i>{{chat.product_name}}</span>
            </div>

        </td>
        <td>
            <div class="abbr-list" title="{{chat.wait_time_pending}}">{{chat.wait_time_pending}}</div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
        </td>
    </tr>
</table>