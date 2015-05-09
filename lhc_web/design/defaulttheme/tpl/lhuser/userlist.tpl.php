<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users');?></h1>
<?php
	$canEdit = $currentUser->hasAccessTo('lhuser','edituser');
	$canDelete = $currentUser->hasAccessTo('lhuser','deleteuser');
?>
<table class="table" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','E-mail');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
    <?php if ($canEdit) : ?><th width="1%">&nbsp;</th><?php endif;?>
    <?php if ($canDelete) : ?><th width="1%">&nbsp;</th><?php endif;?>
</tr>
</thead>
<?php foreach ($userlist as $user) : ?>
    <tr>
        <td><?php echo $user->id?></td>
        <td><?php echo htmlspecialchars($user->username)?></td>
        <td><?php echo htmlspecialchars($user->email)?></td>
        <td><?php echo $user->lastactivity_ago?> ago</td>
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