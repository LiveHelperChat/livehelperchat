<table class="table table-sm mb-0 table-small table-fixed">
    <thead>
    <tr>
        <th width="30%">
            <a ng-click="lhc.toggleWidgetSort('onop_sort','onl_dsc','onl_asc',true)">
                <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator');?>" class="material-icons">account_box</i>
                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['onop_sort'] != 'onl_dsc' && lhc.toggleWidgetData['onop_sort'] != 'onl_asc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by online status')?>" class="material-icons">{{lhc.toggleWidgetData['onop_sort'] == 'onl_dsc' || lhc.toggleWidgetData['onop_sort'] != 'onl_asc' ? 'trending_up' : 'trending_down'}}</i>
            </a>
        </th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last activity ago');?>" class="material-icons">access_time</i></th>
        <th width="20%">
            <a ng-click="lhc.toggleWidgetSort('onop_sort','ac_dsc','ac_asc',true)">
                <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats');?>" class="material-icons chat-active">chat</i>
                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['onop_sort'] != 'ac_dsc' && lhc.toggleWidgetData['onop_sort'] != 'ac_asc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by active chats number')?>" class="material-icons">{{lhc.toggleWidgetData['onop_sort'] == 'ac_dsc' || lhc.toggleWidgetData['onop_sort'] != 'ac_asc' ? 'trending_up' : 'trending_down'}}</i>
            </a>
        </th>
        <th width="30%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
    </tr>
    </thead>
    <tr ng-repeat="operator in online_op.list track by operator.id">
        <td>
            <?php if ($currentUser->hasAccessTo('lhgroupchat','use')) : ?>
            <a ng-show="operator.user_id != <?php echo erLhcoreClassUser::instance()->getUserID();?>" href="#" ng-click="lhc.startChatOperator(operator.user_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Start chat');?>"><i class="material-icons">chat</i></a>
            <?php endif; ?>
            <i class="material-icons<?php if ($currentUser->hasAccessTo('lhuser','changeopstatus')) : ?> action-image<?php endif;?>" <?php if ($currentUser->hasAccessTo('lhuser','changeopstatus')) : ?>ng-click="lhc.openModal('user/setopstatus/' + operator.user_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Change operator status');?>" <?php endif;?> >{{operator.hide_online == 1 ? 'flash_off' : 'flash_on'}}</i>{{operator.hide_online == 1 ? operator.offline_since : ''}} {{operator.name_official}}</td>
        <td>
            <div class="abbr-list" title="{{operator.lastactivity_ago}}">{{operator.lastactivity_ago}}</div>
        </td>
        <td>{{operator.active_chats}}</td>
        <td><div class="abbr-list" title="{{operator.departments_names.join(', ')}}">{{operator.departments_names.join(", ")}}</div></td>

    </tr>
</table>