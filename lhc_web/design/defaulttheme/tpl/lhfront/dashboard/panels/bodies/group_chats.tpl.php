<table class="table table-sm mb-0 table-small table-fixed">
    <thead>
    <tr>
        <th width="40%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Name');?>" class="material-icons">group</i>
        </th>
        <th width="20%">
            <i title="Time ago" class="material-icons">access_time</i>
        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in group_chats.list track by chat.id">
        <td>
            <a class="action-image" ng-click="lhc.startGroupChat(chat.id,chat.name)"><i class="material-icons">chat</i> {{chat.name}}</a>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.time_front}}">{{chat.time_front}}</div>
        </td>
    </tr>
</table>