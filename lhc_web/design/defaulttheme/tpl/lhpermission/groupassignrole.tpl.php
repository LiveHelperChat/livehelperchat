<form action="<?=erLhcoreClassDesign::baseurl('/user/editgroup/'.$group_id)?>" method="post">
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th width="1%">ID</th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Title');?></th>
</tr>
<? foreach (erLhcoreClassGroupRole::getGroupNotAssignedRoles($group_id) as $role) : ?>
    <tr>
        <td><input type="checkbox" name="RoleID[]" value="<?=$role['id']?>"></td>
        <td><?=$role['name']?></td> 
    </tr>
<? endforeach; ?>
</table>
<br />
<div>
<input type="submit" class="default-button" name="AssignRoles" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Assign');?>" />
</div>
</form>