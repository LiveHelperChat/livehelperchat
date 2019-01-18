<table class="table table-sm mb-0 table-small table-fixed"  ng-if="unread_chats.list.length > 0">
    <thead>
    <tr>
        <th width="50%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="material-icons">face</i></th>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
        <th width="30%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Time ago');?>" class="material-icons">access_time</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
    </tr>
    </thead>
    <tr ng-repeat="chat in unread_chats.list track by chat.id" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': chat.user_status_front == 0}">
        <td>
            <div class="abbr-list"><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" ng-click="lhc.startChatNewWindow(chat.id,chat.nick)">open_in_new</a><a ng-click="lhc.previewChat(chat.id)" class="material-icons">info_outline</a><a ng-click="lhc.startChat(chat.id,chat.nick)" title="{{chat.nick}}"> {{chat.nick}} </a></div>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
        <td>
            <div class="abbr-list" title="{{chat.unread_time.hours}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> {{chat.unread_time.minits}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> {{chat.unread_time.seconds}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.">{{chat.unread_time.hours}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> {{chat.unread_time.minits}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> {{chat.unread_time.seconds}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}</div>
        </td>
    </tr>
</table>