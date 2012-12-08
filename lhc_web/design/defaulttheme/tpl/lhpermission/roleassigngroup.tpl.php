<form action="<?=erLhcoreClassDesign::baseurl('/permission/editrole/'.$role_id)?>" method="post">
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th width="1%">ID</th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roleassigngroup','Title');?></th>
</tr>
<? foreach (erLhcoreClassGroupRole::getRoleNotAssignedGroups($role_id) as $group) : ?>
    <tr>
        <td><input type="checkbox" name="GroupID[]" value="<?=$group['id']?>"></td>
        <td><?=$group['name']?></td> 
    </tr>
<? endforeach; ?>
</table>
<br />
<div>
<input type="submit" class="default-button" name="AssignGroups" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roleassigngroup','Assign');?>" />
</div>
</form>