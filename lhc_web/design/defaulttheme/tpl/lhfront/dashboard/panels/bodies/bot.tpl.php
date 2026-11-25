<table class="table table-sm mb-0 table-small table-fixed list-chat-table">
    <thead>
    <tr>
        <th width="60%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i>
        </th>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
        <th width="20%">
            <a ng-click="lhc.toggleWidgetSort('bot_chats_sort','lmt_dsc','lmt_asc',true)">
                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['bot_chats_sort'] != 'lmt_asc' && lhc.toggleWidgetData['bot_chats_sort'] != 'lmt_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by last message time')?>" class="material-icons">{{lhc.toggleWidgetData['bot_chats_sort'] == 'lmt_dsc' || lhc.toggleWidgetData['bot_chats_sort'] != 'lmt_asc' ? 'trending_up' : 'trending_down'}}</i>
            </a>
            <a ng-click="lhc.toggleWidgetSort('bot_chats_sort','id_dsc','id_asc',true)">
                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['bot_chats_sort'] != 'id_asc' && lhc.toggleWidgetData['bot_chats_sort'] != 'id_dsc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by chat start time')?>" class="material-icons">{{lhc.toggleWidgetData['bot_chats_sort'] == 'id_dsc' || lhc.toggleWidgetData['bot_chats_sort'] != 'id_asc' ? 'trending_up' : 'trending_down'}}</i>
            </a>
        </th>
        <th width="20%">
            <i title="Department" class="material-icons">home</i>
            <div class="float-end expand-actions">
                <a ng-click="lhc.changeWidgetHeight('botd',true)" class="text-muted disable-select">
                    <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More rows')?>" class="material-icons">expand</i>
                </a>
                <a ng-click="lhc.changeWidgetHeight('botd',false)" class="text-muted disable-select">
                    <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Less rows')?>" class="material-icons">compress</i>
                </a>
            </div>
        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in bot_chats.list track by chat.id" ng-click="lhc.startChat(chat.id,chat.nick)" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': !chat.user_status_front}">
        <td>
            <div class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="[{{chat.id}}] {{chat.time_created_front}}" ng-click="lhc.previewChat(chat.id, $event)" class="material-icons">info_outline</a><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Offline request');?>" ng-show="chat.status_sub == 7">mail</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Number of messages by user');?>">[{{chat.msg_v || 0}}]</span>&nbsp;<i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More than');?> {{lhc.bot_st.msg_nm}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','user messages');?>" ng-show="chat.msg_v > lhc.bot_st.msg_nm" class="material-icons text-warning">whatshot</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/icon.tpl.php'));?>{{chat.nick}}</div>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
        <td>
            <div class="abbr-list" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Chat started at');?> - {{chat.time_created_front}}">
                <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Receive or send indicator and time since it happened');?>" ng-class="{'text-danger' : chat.pnd_rsp}"}>{{chat.pnd_rsp === true ? 'call_received' : 'call_made'}}</span>
                {{chat.last_msg_time_front ? chat.last_msg_time_front : '&#x2709;'}}
            </div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
        </td>
    </tr>
</table>