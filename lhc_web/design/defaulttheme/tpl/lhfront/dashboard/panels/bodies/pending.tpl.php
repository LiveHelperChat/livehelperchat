<table class="table table-sm mb-0 table-small table-fixed list-chat-table" ng-if="pending_chats.list.length > 0">
    <thead>
    <tr>
        <th width="40%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor')?>" class="material-icons">face</i><a ng-click="lhc.toggleWidgetSort('pending_chats_sort','id_dsc','id_asc',true)"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort')?>" class="material-icons">{{lhc.toggleWidgetData['pending_chats_sort'] == 'id_dsc' ? 'trending_up' : 'trending_down'}}</i></a></th>
        <?php $additionalChatColumnOptions = ['enable_sort' => true, 'sort_field' => 'pending_chats_sort'];?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_header.tpl.php'));?>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Wait time')?>" class="material-icons">access_time</i></th>
        <th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i>

            <div class="float-end expand-actions">
                <a ng-click="lhc.changeWidgetHeight('pendingd',true)" class="text-muted disable-select">
                    <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','More rows')?>" class="material-icons">expand</i>
                </a>
                <a ng-click="lhc.changeWidgetHeight('pendingd',false)" class="text-muted disable-select">
                    <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Less rows')?>" class="material-icons">compress</i>
                </a>
            </div>

        </th>
    </tr>
    </thead>
    <tr ng-repeat="chat in pending_chats.list track by chat.id" ng-click="lhc.startChat(chat.id,chat.nick)" ng-class="{'user-away-row': chat.user_status_front == 2, 'user-online-row': !chat.user_status_front}">
        <td>
            <div class="abbr-list" ><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/delete_chat_pending.tpl.php'));?><span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" />&nbsp;</span>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','redirectcontact')) : ?>
                    <a ng-show="chat.can_edit_chat" class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Redirect user to contact form.');?>" ng-click="lhc.redirectContact(chat.id,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Are you sure?');?>',$event)">reply</a>
                <?php endif;?>

                <a ng-click="lhc.previewChat(chat.id,$event)" class="material-icons">info_outline</a><i class="material-icons" title="Offline request" ng-show="chat.status_sub == 7">mail</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/custom_title_multiinclude.tpl.php'));?><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/icon.tpl.php'));?>{{chat.nick}}<small>{{chat.plain_user_name !== undefined ? ' | ' + chat.plain_user_name : ''}}</small></div>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/additional_column_body.tpl.php'));?>
        <td>
            <div class="abbr-list" title="{{chat.wait_time_pending}}">{{chat.wait_time_pending}}</div>
        </td>
        <td>
            <div class="abbr-list" title="{{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}">
                <?php if ($currentUser->hasAccessTo('lhstatistic','statisticdep')) : ?><a class="text-primary" ng-click="lhc.openModal('statistic/departmentstats/'+chat.dep_id,$event)"><i class="material-icons">donut_large</i><?php endif; ?>
                {{chat.department_name}}{{chat.product_name ? ' | '+chat.product_name : ''}}
                <?php if ($currentUser->hasAccessTo('lhstatistic','statisticdep')) : ?></a><?php endif; ?>
            </div>
        </td>
    </tr>
</table>