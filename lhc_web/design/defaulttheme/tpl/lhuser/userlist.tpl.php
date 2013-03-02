<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users');?></h1>

<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','E-mail');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($userlist as $user) : ?>
    <tr>
        <td><?php echo $user->id?></td>
        <td><?php echo htmlspecialchars($user->username)?></td>
        <td><?php echo htmlspecialchars($user->email)?></td>
        <td><?php echo $user->lastactivity_ago?> ago</td>
        <td><a class="tiny button round" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
        <td><a class="tiny alert button round" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('user/delete')?>/<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>
<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<br />

<div>
<a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('user/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','New user');?></a>
</div>
