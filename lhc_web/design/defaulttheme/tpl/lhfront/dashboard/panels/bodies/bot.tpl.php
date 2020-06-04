<table class="table table-sm mb-0 table-small table-fixed">
    <thead>
    <tr>
        <th width="40%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i>
        </th>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
        <th width="20%">
            <i title="Time ago" class="material-icons">access_time</i>
        </th>
        <th width="20%">
            <i title="Department" class="material-icons">home</i>
        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in bot_chats.list track by chat.id" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
        <td>
            <div class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">open_in_new</a><a title="[{{chat.id}}] {{chat.time_created_front}}" ng-click="lhc.previewChat(chat.id)" class="material-icons">info_outline</a> <a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Offline request');?>" ng-show="chat.status_sub == 7">mail</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?>&nbsp;<span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Number of messages by user');?>">[{{chat.msg_v}}]</span>&nbsp;<i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More than');?> {{lhc.bot_st.msg_nm}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','user messages');?>" ng-show="chat.msg_v > lhc.bot_st.msg_nm" class="material-icons text-warning">whatshot</i>{{chat.nick}}</a></div>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
        <td>
            <div class="abbr-list" title="{{chat.time_created_front}}">{{chat.time_created_front}}</div>
        </td>

        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
        </td>
    </tr>
</table>