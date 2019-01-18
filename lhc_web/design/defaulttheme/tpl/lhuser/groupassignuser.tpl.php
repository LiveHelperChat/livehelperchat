<?php if (isset($assigned) && $assigned == true) : ?>
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','User was assigned to the group!'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<script>
setTimeout(function(){
    parent.document.location = '<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group_id?>';
},2000);
</script>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser')?>/<?php echo $group_id?>" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<table class="table" cellpadding="0" cellspacing="0" width="100%">
<thead>
    <tr>
        <th>ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Username');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','E-mail');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Surname');?></th>
    </tr>
</thead>
<?php foreach (erLhcoreClassGroupUser::getGroupNotAssignedUsers($group_id) as $user) : ?>
    <tr>
        <td><input type="checkbox" name="UserID[]" value="<?php echo $user['id']?>"></td>
        <td><?php echo htmlspecialchars($user['username'])?></td>
        <td><?php echo htmlspecialchars($user['email'])?></td>
        <td><?php echo htmlspecialchars($user['name'])?></td>
        <td><?php echo htmlspecialchars($user['surname'])?></td>
    </tr>
<?php endforeach; ?>
</table>
<br />
<input type="submit" class="btn btn-default" name="AssignUsers" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Assign');?>" />

</form>