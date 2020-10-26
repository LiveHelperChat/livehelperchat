<table class="table table-sm mb-0 table-small table-fixed" ng-if="pending_mails.list.length > 0">
    <thead>
    <tr>
        <th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor')?>" class="material-icons">face</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time')?>" class="material-icons">access_time</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
    </tr>
    </thead>
    <tr ng-repeat="chat in pending_mails.list track by chat.id">
        <td>
            <div class="abbr-list" ><a title="{{chat.id}}" ng-click="lhc.previewMail(chat.id)" class="material-icons">info_outline</a><a ng-click="lhc.startMailChat(chat.id,chat.subject)" title="{{chat.from_address}}">{{chat.from_name}} | {{chat.subject}}</a><small>{{chat.plain_user_name !== undefined ? ' | ' + chat.plain_user_name : ''}}</small></div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.wait_time_pending}}">{{chat.wait_time_pending}}</div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">{{chat.department_name}}</div>
        </td>
    </tr>
</table>