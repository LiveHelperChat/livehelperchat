<table class="table table-sm mb-0 table-small table-fixed">
    <thead>
    <tr>
        <th width="50%">
            <a ng-click="lhc.toggleWidgetSort('onop_sort','onl_dsc','onl_asc',true)">
                <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator');?>" class="material-icons">account_box</i>
                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['onop_sort'] != 'onl_dsc' && lhc.toggleWidgetData['onop_sort'] != 'onl_asc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by online status')?>" class="material-icons">{{lhc.toggleWidgetData['onop_sort'] == 'onl_dsc' || lhc.toggleWidgetData['onop_sort'] != 'onl_asc' ? 'trending_up' : 'trending_down'}}</i>
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/online_operators_status_sort_multiinclude.tpl.php')); ?>
            </a>
        </th>
        <th width="5%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last activity ago');?>" class="material-icons">access_time</i></th>
        <th width="15%">
            <a ng-click="lhc.toggleWidgetSort('onop_sort','ac_dsc','ac_asc',true)">
                <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats');?>" class="material-icons chat-active">chat</i>
                <i ng-class="{'text-muted' : (lhc.toggleWidgetData['onop_sort'] != 'ac_dsc' && lhc.toggleWidgetData['onop_sort'] != 'ac_asc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by active chats number')?>" class="material-icons">{{lhc.toggleWidgetData['onop_sort'] == 'ac_dsc' || lhc.toggleWidgetData['onop_sort'] != 'ac_asc' ? 'trending_up' : 'trending_down'}}</i>
            </a>
        </th>
        <th width="30%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i>

            <div class="float-end expand-actions">
                <a ng-click="lhc.changeWidgetHeight('operatord',true)" class="text-muted disable-select">
                    <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More rows')?>" class="material-icons">expand</i>
                </a>
                <a ng-click="lhc.changeWidgetHeight('operatord',false)" class="text-muted disable-select">
                    <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Less rows')?>" class="material-icons">compress</i>
                </a>
            </div>

        </th>
    </tr>
    </thead>
    <tr ng-repeat="operator in online_op.list track by operator.id">
        <td>
            <img class="rounded-circle" ng-if="operator.avatar" ng-src="{{operator.avatar}}" alt="" width="20" />

            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/online_operators_status_multiinclude.tpl.php')); ?>

            <?php if ($currentUser->hasAccessTo('lhgroupchat','use')) : ?>
            <a ng-show="operator.user_id != <?php echo erLhcoreClassUser::instance()->getUserID();?>" href="#" ng-click="lhc.startChatOperator(operator.user_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Start chat');?>"><i class="material-icons">chat</i></a>
            <?php endif; ?>
            <i class="material-icons<?php if ($currentUser->hasAccessTo('lhuser','setopstatus')) : ?> action-image<?php endif;?>" <?php if ($currentUser->hasAccessTo('lhuser','setopstatus')) : ?>ng-click="lhc.openModal('user/setopstatus/' + operator.user_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Change operator status');?>" <?php endif;?> >{{operator.hide_online == 1 ? 'flash_off' : 'flash_on'}}</i>

            <?php if ($currentUser->hasAccessTo('lhstatistic','userstats')) : ?><a ng-class="{'text-muted' : operator.ro}" href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','See operator statistic')?>" ng-click="lhc.openModal('statistic/userstats/'+operator.user_id)"><?php endif; ?>
                {{operator.hide_online == 1 ? operator.offline_since : ''}} {{operator.name_official}}
            <?php if ($currentUser->hasAccessTo('lhstatistic','userstats')) : ?></a><?php endif; ?>
        </td>
        <td class="align-middle">
            <div class="abbr-list" title="{{operator.lastactivity_ago}}">{{operator.lastactivity_ago}}</div>
        </td>
        <td title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Max');?> - {{operator.max_chats && operator.max_chats > 0 ? operator.max_chats : 'n/a'}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','chats');?>" ng-class="{'align-middle': true, 'text-danger' : (operator.max_chats && operator.max_chats > 0 && operator.max_chats - operator.active_chats <= 0),'text-success' : (operator.max_chats && operator.max_chats > 0 && operator.max_chats - operator.active_chats >= 1)}">
            {{operator.active_chats}} <abbr title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','a.c')?></abbr>, {{operator.max_chats && operator.max_chats > 0 ? (operator.max_chats - operator.active_chats) : ' n/a'}} <abbr title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Free slots')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','f.s')?></abbr>
        </td>
        <td class="align-middle"><div class="abbr-list" title="{{operator.departments_names.join(', ')}}">{{operator.departments_names.join(", ")}}</div></td>

    </tr>
</table>