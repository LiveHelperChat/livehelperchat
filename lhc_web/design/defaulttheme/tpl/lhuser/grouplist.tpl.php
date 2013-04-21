<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Groups');?></h1>
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th>ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Name');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($groups as $group) : ?>
    <tr>
        <td width="1%"><?php echo $group->id?></td>
        <td><?php echo htmlspecialchars($group->name)?></td>
        <td nowrap><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Edit group');?></a></td>
        <td nowrap><a class="small alert button round" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('gallery/album_list_admin','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('user/deletegroup')?>/<?php echo $group->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Delete group');?></a></td>
    </tr>
<?php endforeach; ?>
</table>
<br />
<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('user/newgroup')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','New group');?></a>
