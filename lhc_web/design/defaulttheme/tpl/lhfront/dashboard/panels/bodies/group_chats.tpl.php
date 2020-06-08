<table class="table table-sm mb-0 table-small table-fixed">
    <thead>
    <tr>
        <th width="40%">
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Name');?>" class="material-icons">group</i>
        </th>
        <th width="20%">

        </th>
        <th width="20%">
            <i title="Time ago" class="material-icons">access_time</i>
        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in group_chats.list track by chat.id">
        <td>
            <a class="d-block action-image" ng-click="lhc.startGroupChat(chat.id,chat.name)">

                <i class="material-icons text-warning" ng-if="chat.is_member == true && (!chat.ls_id || chat.ls_id < chat.last_msg_id)">whatshot</i>

                <i class="material-icons">{{chat.type == 1 ? 'security' : 'public'}}</i> <i ng-if="chat.user_id == current_user_id" class="material-icons">account_balance</i>[{{chat.tm}}] {{chat.name}}</a>
        </td>
        <td>
            <button ng-if="chat.is_member == true && chat.jtime == 0" title="Accept invitation and join private chat" class="btn btn-xs btn-info" ng-click="lhc.startGroupChat(chat.id,chat.name)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept invite');?></button>
            <button ng-if="chat.is_member == true && chat.jtime == 0" title="Reject for private chat" class="btn btn-xs btn-danger" ng-click="lhc.rejectGroupChat(chat.id)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Reject invite');?></button>
            <button ng-if="chat.is_member == false && chat.jtime == 0" title="You can join public chat" class="btn btn-xs btn-info" ng-click="lhc.startGroupChat(chat.id,chat.name)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Join public chat');?></button>
            <button ng-if="chat.is_member == true && chat.jtime > 0" title="You are member of this group chat" class="btn btn-xs btn-secondary" ng-click="lhc.startGroupChat(chat.id,chat.name)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Already a member');?></button>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.time_front}}">{{chat.time_front}}</div>
        </td>
    </tr>
    <tr>
        <td>
            <input type="text" name="" class="form-control form-control-sm" ng-model="lhc.new_group_name" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Your new group name');?>">
        </td>
        <td>
            <select class="form-control form-control-sm" ng-init="lhc.new_group_type='1'" ng-model="lhc.new_group_type">
                <option value="1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Private group');?></option>
                <?php if ($currentUser->hasAccessTo('lhgroupchat','public_chat')) : ?>
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Public group');?></option>
                <?php endif; ?>
            </select>
        </td>
        <td>
            <a href="" class="btn btn-sm btn-secondary d-block" ng-click="lhc.startNewGroupChat(lhc.new_group_name,lhc.new_group_type)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','New');?></a>
        </td>
    </tr>
</table>