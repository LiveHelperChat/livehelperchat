<form action="<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser')?>/<?php echo $group_id?>" method="post">
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
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
        <td><?php echo $user['username']?></td>
        <td><?php echo $user['email']?></td>
        <td><?php echo $user['name']?></td>
        <td><?php echo $user['surname']?></td>        
    </tr>
<?php endforeach; ?>
</table>
<br />

<input type="submit" class="small button" name="AssignUsers" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Assign');?>" />

</form>