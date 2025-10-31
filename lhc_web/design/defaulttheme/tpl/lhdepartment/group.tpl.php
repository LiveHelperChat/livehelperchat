<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments groups');?></h1>

<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Group');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Pending chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Active chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Inactive chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Bots chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Online operators active chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Online operators inactive chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Max chats');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Online operators');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Offline operators');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Total operators');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhtheme','administratethemes')) : ?>
        <th width="1%">&nbsp;</th>
        <?php endif; ?>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td nowrap="nowrap">
            <a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','See assigned departments statistic')?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'department/editgroup/<?php echo htmlspecialchars($item->id)?>/(action)/depgroupstats'})">
                <span class="material-icons">bar_chart</span>
            </a><?php echo htmlspecialchars($item->name)?>
        </td>
        <td><?php echo htmlspecialchars($item->pchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->achats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->inachats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->bchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->acopchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->inopchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->max_load)?></td>
        <td>
            <?php
            $filter['ignore_fields'] = array('only_priority','chat_max_priority','chat_min_priority','assign_priority', 'max_mails','last_accepted_mail','exc_indv_autoasign','exclude_autoasign_mails','active_mails','pending_mails','exclude_autoasign','max_chats','dep_group_id','type','ro','dep_id','hide_online_ts','hide_online','last_activity','lastd_activity','always_on','last_accepted','active_chats','pending_chats','inactive_chats','ro');
            echo count(erLhcoreClassModelUserDep::getList([
                    'filter' => ['dep_group_id' => $item->id],
                    'ignore_fields' => $filter['ignore_fields'],
                    'group' => 'user_id, id',
                    'customfilter' => ['(`hide_online` = 0 AND (`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ' OR `always_on` = 1))']
            ])); ?>
        </td>
        <td>
            <?php echo count(erLhcoreClassModelUserDep::getList([
                    'filter' => ['dep_group_id' => $item->id, 'hide_online' => 1],
                    'ignore_fields' => $filter['ignore_fields'],
                    'group' => 'user_id, id',
                    'customfilter' => ['(`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ')']
            ])); ?>
        </td>
        <td>
            <?php $assignedOperator = count(erLhcoreClassModelUserDep::getList([
                    'filter' => ['dep_group_id' => $item->id],
                    'ignore_fields' => $filter['ignore_fields'],
                    'group' => 'user_id, id',
                    'customfilter' => ['(`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ')']
            ]));echo $assignedOperator; ?>
        </td>
        <td nowrap ng-non-bindable>
            <a class="btn btn-secondary btn-xs action-image text-white csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('department/editgroup')?>/<?php echo $item->id?>/(action)/updatestats"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Update stats');?></a>
        </td>
        <td nowrap ng-non-bindable>
            <a class="btn btn-secondary btn-xs action-image text-white" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'department/editgroup/<?php echo htmlspecialchars($item->id)?>/(action)/operators'})" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Assigned operators');?></a>
        </td>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhtheme','administratethemes')) : ?>
        <td nowrap ng-non-bindable>
            <a class="btn btn-secondary btn-xs action-image text-white" onclick='lhc.revealModal({iframe:true, title : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Themes edit')))?>, height : 500, modalbodyclass:"p-0", url:WWW_DIR_JAVASCRIPT+"theme/editthemebydepgroup/<?php echo htmlspecialchars($item->id)?>"})' ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit theme');?></a>
        </td>
        <?php endif; ?>
        <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('department/editgroup')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit');?></a></td>

        <td nowrap>
            <?php if ($assignedOperator == 0) : ?>
                <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('department/deletegroup')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a>
            <?php else : ?>
                <button class="btn btn-danger btn-xs" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Disabled because there is assigned operators to it!');?>" disabled ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></button>
            <?php endif; ?>
        </td>

    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','managegroups')) : ?>
<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('department/newgroup')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
<?php endif;?>