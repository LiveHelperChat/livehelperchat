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
            <div data-toggle="popover" data-placement="top" data-chat-id="{{chat.id}}" class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">open_in_new</a><a title="[{{chat.id}}] {{chat.time_created_front}}" ng-click="lhc.previewChat(chat.id)" class="material-icons">info_outline</a> <a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"><i class="material-icons" title="Offline request" ng-show="chat.status_sub == 7">mail</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?>{{chat.nick}}</a></div>

            <div id="popover-title-{{chat.id}}" class="hide">
                {{chat.nick}} [{{chat.id}}]
            </div>

            <?php if (!isset($optinsPanel['hide_tooltip']) || $optinsPanel['hide_tooltip'] == false) : ?>
                <div id="popover-content-{{chat.id}}" class="hide">
                    <i class="material-icons">access_time</i>{{chat.time_created_front}}<br />
                    <i class="material-icons">account_box</i>{{chat.plain_user_name}}<br />
                    <i class="material-icons">home</i>{{chat.department_name}}<br />
                    <span ng-show="chat.product_name"><i class="material-icons">&#xE8CC;</i>{{chat.product_name}}</span>
                </div>
            <?php endif; ?>
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