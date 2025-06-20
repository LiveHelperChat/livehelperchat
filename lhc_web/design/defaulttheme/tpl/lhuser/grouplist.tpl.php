<h1><?php include(erLhcoreClassDesign::designtpl('lhuser/titles/groups.tpl.php')); ?></h1>
<?php
	$canEdit = $currentUser->hasAccessTo('lhuser','editgroup');
	$canDelete = $currentUser->hasAccessTo('lhuser','deletegroup');
?>
<table class="table table-sm" cellpadding="0" ng-non-bindable cellspacing="0" width="100%">
<thead>
<tr>
    <th>ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Name');?></th>
    <?php if ($canEdit) : ?><th width="1%">&nbsp;</th><?php endif;?>
    <?php if ($canEdit) : ?><th width="1%">&nbsp;</th><?php endif;?>
    <?php if ($canDelete) : ?><th width="1%">&nbsp;</th><?php endif;?>
</tr>
</thead>
<?php foreach ($groups as $group) : ?>
    <tr>
        <td width="1%"><?php echo $group->id?></td>
        <td><?php echo htmlspecialchars($group->name)?></td>
        <?php if ($canEdit) : ?><td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Edit group');?></a></td><?php endif;?>
        <?php if ($canEdit) : ?><td nowrap><a class="btn btn-secondary btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('user/clonegroup')?>/<?php echo $group->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Clone');?></a></td><?php endif;?>
        <?php if ($canDelete) : ?><td nowrap><?php if ($group->id != 1) : ?><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('user/deletegroup')?>/<?php echo $group->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Delete group');?></a><?php endif;?></td><?php endif;?>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<br />
<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if ($currentUser->hasAccessTo('lhuser','creategroup')) : ?>
<a class="btn btn-sm btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('user/newgroup')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','New group');?></a>
<?php endif;?>