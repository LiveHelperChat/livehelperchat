<table class="table table-sm mb-0 table-small table-fixed list-chat-table">
    <thead>
    <tr>
        <th width="27%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i>
        </th>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
        <th width="25%">
            <span class="material-icons">label</span>
        </th>
        <th width="18%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Time since last message')?>" class="material-icons">access_time</i>
        </th>
        <th width="10%">
            <i title="Department" class="material-icons">home</i>
        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in subject_chats.list track by chat.id" ng-click="lhc.startChat(chat.id,chat.nick)" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': !chat.user_status_front}">
        <td>
            <div class="abbr-list">
                <span class="material-icons chat-pending" ng-class="{'chat-active': chat.status == 1 || chat.status == 5}">{{chat.status == 5 ? 'android' : 'chat'}}</span>
                <span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="[{{chat.id}}] {{chat.time_created_front}}" ng-click="lhc.previewChat(chat.id, $event)" class="material-icons">info_outline</a><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Offline request');?>" ng-show="chat.status_sub == 7">mail</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?>&nbsp;<i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More than');?> {{lhc.bot_st.msg_nm}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','user messages');?>" ng-show="chat.msg_v > lhc.bot_st.msg_nm" class="material-icons text-warning">whatshot</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/icon.tpl.php'));?>{{chat.nick}}<small>{{chat.plain_user_name !== undefined ? ' | ' + chat.plain_user_name : ''}}</small>
            </div>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
        <td>
            <span ng-if="chat.subject_list" class="badge bg-info fs12 me-1" ng-repeat="subjectitem in chat.subject_list track by $index">{{subjectitem}}</span>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.time_created_front}}">
                <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Receive or send indicator and time since it happened');?>" ng-class="{'text-danger' : chat.pnd_rsp}"}>{{chat.pnd_rsp === true ? 'call_received' : 'call_made'}}</span>
                {{chat.last_msg_time_front ? chat.last_msg_time_front : '&#x2709;'}}<span class="ps-1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time or time since chat was started')?>" ng-if="chat.status == 1 || !chat.status">| {{chat.start_last_action_front}}</span>
            </div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
        </td>
    </tr>
</table>