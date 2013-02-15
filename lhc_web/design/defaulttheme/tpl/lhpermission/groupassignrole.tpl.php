<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group_id?>" method="post">
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Title');?></th>
</tr>
</thead>
<?php foreach (erLhcoreClassGroupRole::getGroupNotAssignedRoles($group_id) as $role) : ?>
    <tr>
        <td><input type="checkbox" name="RoleID[]" value="<?php echo $role['id']?>"></td>
        <td><?php echo htmlspecialchars($role['name'])?></td> 
    </tr>
<?php endforeach; ?>
</table>

<input type="submit" class="small button" name="AssignRoles" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Assign');?>" />

</form>