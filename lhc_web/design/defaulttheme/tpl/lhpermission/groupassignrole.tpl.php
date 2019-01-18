<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group_id?>" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<table class="table" cellpadding="0" cellspacing="0" width="100%">
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

<input type="submit" class="btn btn-default" name="AssignRoles" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/groupassignrole','Assign');?>" />

</form>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>