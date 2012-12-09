<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Group edit');?> - <?php echo $group->name?></legend>

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/user/editgroup/'.$group->id)?>" method="post">
			<table>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="<?php echo htmlspecialchars($group->name);?>" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Update');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</fieldset>

<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assigned users');?> - <?php echo $group->name?></legend>
<form action="<?php echo erLhcoreClassDesign::baseurl('/user/editgroup/'.$group->id)?>" method="post">

<table class="lentele" cellpadding="0" cellspacing="0">
<tr>
    <th>&nbsp;</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Surname');?></th>
</tr>
<?php foreach (erLhcoreClassGroupUser::getGroupUsers($group->id) as $UserAssigned) : ?>
<tr>
    <td><input type="checkbox" name="AssignedID[]" value="<?php echo $UserAssigned['assigned_id']?>" /></td>
    <td><?php echo $UserAssigned['username']?></td>
    <td><?php echo $UserAssigned['name']?></td>
    <td><?php echo $UserAssigned['surname']?></td>
</tr>
<?php endforeach; ?>

</table>
<div>
<br />
<input type="submit" class="default-button" name="Remove_user_from_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Remove user from group');?>" /> <input class="default-button" type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assign user');?>" onclick="lhinst.abstractDialog('assign-user-dialog','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','User assignment');?>','<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser/'.$group->id)?>')" />
</div>
</form>
</fieldset>

<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assigned roles');?> - <?php echo $group->name?></legend>
<form action="<?php echo erLhcoreClassDesign::baseurl('/user/editgroup/'.$group->id)?>" method="post">

<table class="lentele" cellpadding="0" cellspacing="0">
<tr>
    <th>&nbsp;</th>
    <th>Name</th>
</tr>
<?php foreach (erLhcoreClassGroupRole::getGroupRoles($group->id) as $UserAssigned) : ?>
<tr>
    <td><input type="checkbox" name="AssignedID[]" value="<?php echo $UserAssigned['assigned_id']?>" /></td>
    <td><?php echo $UserAssigned['name']?></td>
</tr>
<?php endforeach; ?>

</table>
<div>
<br />
<input type="submit" class="default-button" name="Remove_role_from_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Remove role from group');?>" /> <input class="default-button" type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assign role');?>" onclick="lhinst.abstractDialog('assign-role-dialog','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Role assignment');?>','<?php echo erLhcoreClassDesign::baseurl('permission/groupassignrole/'.$group->id)?>')" />
</div>
</form>
</fieldset>



<div id="assign-user-dialog"></div>
<div id="assign-role-dialog"></div>

<?php if (isset($adduser)) : ?>
<script type="text/javascript">
$(function() {
    lhinst.abstractDialog('assign-user-dialog','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','User assignment');?>','<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser/'.$group->id)?>');
})
</script>
<?php endif; ?>