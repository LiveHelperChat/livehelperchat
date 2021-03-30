<div ng-if="my_chats && my_chats.list.length > 0" class="panel-list">
    <table class="table table-sm mb-0 table-small table-fixed list-chat-table">
        <thead>
        <tr>
            <th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i></th>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
            <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last message');?>" class="material-icons">access_time</i></th>
            <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
        </tr>
        </thead>
        <tr ng-repeat="chat in my_chats.list track by chat.id" ng-click="lhc.startChat(chat.id,chat.nick)" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': !chat.user_status_front}">
            <td>
                <div class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a ng-click="lhc.previewChat(chat.id,$event);" class="material-icons">info_outline</a><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Has unread messages');?>" ng-if="chat.hum" class="material-icons text-danger">feedback</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/icon.tpl.php'));?>{{chat.nick}}</div>
            </td>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
            <td>
                <div class="abbr-list" title="{{chat.status == 1 ? 'Active' : 'Pending'}}">
                    <i ng-if="chat.status != 1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Pending chat');?>" class="material-icons chat-unread">&#xE80E;</i>
                    <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Receive or send indicator and time since it happened');?>" ng-class="{'text-danger' : chat.pnd_rsp}"}>{{chat.pnd_rsp === true ? 'call_received' : 'call_made'}}</span>
                    {{chat.status == 0 ? '&#x23F3; '+chat.wait_time_pending : chat.last_msg_time_front}}
                </div>
            </td>
            <td>
                <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
            </td>
        </tr>
    </table>
</div>