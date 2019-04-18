<table class="table table-sm mb-0 table-small table-fixed">
    <thead>
    <tr>
        <th width="40%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">&#xf643;</i>
        </th>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
        <th width="20%">
            <i title="Time ago" class="material-icons">&#xf150;</i>
        </th>
        <th width="20%">
            <i title="Department" class="material-icons">&#xf2dc;</i>
        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in bot_chats.list track by chat.id" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
        <td>
            <div class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">&#xf3cc;</a><a title="[{{chat.id}}] {{chat.time_created_front}}" ng-click="lhc.previewChat(chat.id)" class="material-icons">&#xf2fd;</a> <a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"><i class="material-icons" title="Offline request" ng-show="chat.status_sub == 7">&#xf5ef;</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?>{{chat.nick}}</a></div>
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