<h1><?php include(erLhcoreClassDesign::designtpl('lhuser/titles/userlist.tpl.php')); ?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/search_panel.tpl.php')); ?>

<?php
	$canEdit = $currentUser->hasAccessTo('lhuser','edituser');
	$canDelete = $currentUser->hasAccessTo('lhuser','deleteuser');
?>
<table class="table" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username (Nickname)');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','E-mail');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/column_multiinclude.tpl.php')); ?>
    <?php if ($canEdit) : ?><th width="1%">&nbsp;</th><?php endif;?>
    <?php if ($canDelete) : ?><th width="1%">&nbsp;</th><?php endif;?>
</tr>
</thead>
<?php foreach ($userlist as $user) : ?>
    <tr>
        <td><?php echo $user->id?></td>
        <td><?php echo htmlspecialchars($user->username)?><?php echo htmlspecialchars($user->chat_nickname !== '' ? ' ('. $user->chat_nickname .')' : '')?></td>
        <td><?php echo htmlspecialchars($user->email)?></td>
        <td><?php echo $user->lastactivity_ago?> ago</td>
        <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/column_data_multiinclude.tpl.php')); ?>
        <?php if ($canEdit) : ?><td><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td><?php endif;?>
        <?php if ($canDelete) : ?><td><?php if ($user->id != 1) : ?><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('user/delete')?>/<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a><?php endif;?></td><?php endif;?>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<br />

<?php if ($currentUser->hasAccessTo('lhuser','createuser')) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/new.tpl.php')); ?>
<?php endif; ?>