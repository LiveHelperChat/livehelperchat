<table class="table table-sm mb-0 table-small table-fixed" ng-if="alarm_mails.list.length > 0">
    <thead>
    <tr>
        <th width="22%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor')?>" class="material-icons">face</i></th>
        <th width="30%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time')?>" class="material-icons">label</i></th>
        <th width="18%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time')?>" class="material-icons">access_time</i></th>
        <th width="15%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator')?>" class="material-icons">face</i></th>
        <th width="15%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
    </tr>
    </thead>
    <tr ng-repeat="chat in alarm_mails.list track by chat.id">
        <td>
            <div class="abbr-list" >
                <a title="{{chat.id}}" ng-click="lhc.previewMail(chat.id)" class="material-icons">info_outline</a><a ng-click="lhc.startMailChat(chat.id,chat.subject_front)" title="{{chat.from_address}} |  {{chat.subject_front}}"><i class="material-icons" ng-class="chat.status == 1 ? 'chat-active' : 'chat-pending'">mail_outline</i>{{chat.from_name}} | {{chat.subject_front}}</a>
            </div>
        </td>
        <td>
            <div class="abbr-list">
                <span ng-if="chat.subject_list" class="badge bg-info fs12 me-1" ng-repeat="subjectitem in chat.subject_list track by $index">{{subjectitem}}</span>
            </div>
        </td>
        <td>
            <div class="abbr-list">
                {{!chat.status ? chat.wait_time_pending : chat.wait_time_response}}
            </div>
        </td>
        <td>
            <div class="abbr-list">{{chat.plain_user_name}}</div>
        </td>
        <td>
            <div class="abbr-list">{{chat.department_name}}</div>
        </td>
    </tr>
</table>