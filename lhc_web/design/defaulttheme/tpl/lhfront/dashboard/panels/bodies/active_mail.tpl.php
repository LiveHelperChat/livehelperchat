<table class="table table-sm mb-0 table-small table-fixed" ng-if="active_mails.list.length > 0">
    <thead>
    <tr>
        <th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor')?>" class="material-icons">face</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Create time')?>" class="material-icons">access_time</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator')?>" class="material-icons">face</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
    </tr>
    </thead>
    <tr ng-repeat="chat in active_mails.list track by chat.id">
        <td>
            <div class="abbr-list" ><a title="{{chat.id}}" ng-click="lhc.previewMail(chat.id)" class="material-icons">info_outline</a><a ng-click="lhc.startMailChat(chat.id,chat.subject)" title="{{chat.from_address}}">{{chat.from_name}} | {{chat.subject}}</a></div>
        </td>
        <td>
            <div class="abbr-list" >{{chat.pnd_time_front}}</div>
        </td>
        <td>
            <div class="abbr-list" >{{chat.plain_user_name}}</div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}">{{chat.department_name}}</div>
        </td>
    </tr>
</table>