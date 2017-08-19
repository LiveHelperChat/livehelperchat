<h1><?php include(erLhcoreClassDesign::designtpl('lhuser/titles/userlist.tpl.php')); ?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/search_panel.tpl.php')); ?>

<?php
	$canEdit = $currentUser->hasAccessTo('lhuser','edituser');
?>

<table class="table" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Active chats');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Pending chats');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Inactive chats');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','RT Active chats');?></th>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/column_multiinclude.tpl.php')); ?>
    <?php if ($canEdit) : ?><th width="1%">&nbsp;</th><?php endif;?>
</tr>
</thead>
<?php foreach ($userlist as $user) : ?>
    <tr>
        <td><?php echo $user->id?></td>
        <td><?php echo htmlspecialchars($user->username)?></td>
        <td>
            <?php echo isset($userlist_stats[$user->id]['ac']) ? $userlist_stats[$user->id]['ac'] : '-'?>
        </td>
        <td>
            <?php echo isset($userlist_stats[$user->id]['pc']) ? $userlist_stats[$user->id]['pc'] : '-'?>
        </td>
        <td>
            <?php echo isset($userlist_stats[$user->id]['ic']) ? $userlist_stats[$user->id]['ic'] : '-'?>
        </td>
        <td>
            <?php if (isset($userlist_stats[$user->id]['acrt']) && isset($userlist_stats[$user->id]['ac']) && $userlist_stats[$user->id]['ac'] != $userlist_stats[$user->id]['acrt']) : ?><i class="material-icons">&#xE002;</i><?php endif;?><?php echo isset($userlist_stats[$user->id]['acrt']) ? $userlist_stats[$user->id]['acrt'] : '-'?>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/column_data_multiinclude.tpl.php')); ?>
        <?php if ($canEdit) : ?><td><a class="btn btn-default btn-xs" href="?ustats=<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Update stats');?></a></td><?php endif;?>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<br />