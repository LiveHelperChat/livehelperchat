<form action="<?php echo erLhcoreClassDesign::baseurl('/user/editgroup/'.$group_id)?>" method="post">
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Title');?></th>
</tr>
<?php foreach (erLhcoreClassGroupRole::getGroupNotAssignedRoles($group_id) as $role) : ?>
    <tr>
        <td><input type="checkbox" name="RoleID[]" value="<?php echo $role['id']?>"></td>
        <td><?php echo $role['name']?></td> 
    </tr>
<?php endforeach; ?>
</table>
<br />
<div>
<input type="submit" class="default-button" name="AssignRoles" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Assign');?>" />
</div>
</form>