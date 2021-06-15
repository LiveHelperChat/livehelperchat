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