<form action="<?php echo erLhcoreClassDesign::baseurl('/permission/editrole/'.$role_id)?>" method="post">
<table cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roleassigngroup','Title');?></th>
</tr>
</thead>
<?php foreach (erLhcoreClassGroupRole::getRoleNotAssignedGroups($role_id) as $group) : ?>
    <tr>
        <td><input type="checkbox" name="GroupID[]" value="<?php echo $group['id']?>"></td>
        <td><?php echo $group['name']?></td> 
    </tr>
<?php endforeach; ?>
</table>
<br />

<input type="submit" class="small button" name="AssignGroups" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roleassigngroup','Assign');?>" />

</form>