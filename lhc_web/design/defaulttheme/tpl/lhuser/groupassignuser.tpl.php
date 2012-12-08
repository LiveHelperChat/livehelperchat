<form action="<?=erLhcoreClassDesign::baseurl('/user/editgroup/'.$group_id)?>" method="post">
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th>ID</th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Username');?></th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','E-mail');?></th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Name');?></th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Surname');?></th>
</tr>
<? foreach (erLhcoreClassGroupUser::getGroupNotAssignedUsers($group_id) as $user) : ?>
    <tr>
        <td><input type="checkbox" name="UserID[]" value="<?=$user['id']?>"></td>
        <td><?=$user['username']?></td>
        <td><?=$user['email']?></td>
        <td><?=$user['name']?></td>
        <td><?=$user['surname']?></td>        
    </tr>
<? endforeach; ?>
</table>
<br />
<div>
<input type="submit" class="default-button" name="AssignUsers" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/groupassignuser','Assign');?>" />
</div>
</form>