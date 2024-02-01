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
        <td><?php echo htmlspecialchars($item->name)?></td>
        <td><?php echo htmlspecialchars($item->pchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->achats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->inachats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->bchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->acopchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->inopchats_cnt)?></td>
        <td><?php echo htmlspecialchars($item->max_load)?></td>
        <td>
            <?php echo count(erLhcoreClassModelUserDep::getList([
                    'filter' => ['dep_group_id' => $item->id],
                    'group' => 'user_id',
                    'customfilter' => ['(`hide_online` = 0 AND (`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ' OR `always_on` = 1))']
            ])); ?>
        </td>
        <td>
            <?php echo count(erLhcoreClassModelUserDep::getList([
                    'filter' => ['dep_group_id' => $item->id, 'hide_online' => 1],
                    'group' => 'user_id',
                    'customfilter' => ['(`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ')']
            ])); ?>
        </td>
        <td>
            <?php echo count(erLhcoreClassModelUserDep::getList([
                    'filter' => ['dep_group_id' => $item->id],
                    'group' => 'user_id',
                    'customfilter' => ['(`last_activity` > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ')']
            ])); ?>
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
        <td nowrap><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('gallery/album_list_admin','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('department/deletegroup')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a></td>
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