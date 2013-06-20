<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Group edit');?> - <?php echo htmlspecialchars($group->name)?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div>
	<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" method="post">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Title');?></label>
		<input class="inputfield" type="text" name="Name"  value="<?php echo htmlspecialchars($group->name);?>" />
		
		<input type="submit" class="small button" name="Update_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Update');?>"/>
					
	</form>
</div>

<div class="header-list"><h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assigned users');?> - <?php echo htmlspecialchars($group->name)?></h1></div>
<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" method="post">

<table class="lentele" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th>&nbsp;</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Username');?></th>
</tr>
</thead>
<?php foreach ($users as $UserAssigned) : ?>
<tr>
    <td><input type="checkbox" name="AssignedID[]" value="<?php echo $UserAssigned->id?>" /></td>
    <td><?php echo $UserAssigned->user?></td>
</tr>
<?php endforeach;?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<div>

<input type="submit" class="small button alert" name="Remove_user_from_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Remove user from the group');?>" /> <input class="small button" type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assign user');?>" onclick="$.colorbox({iframe:true,width:'850px',height:'600px', href:'<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser')?>/<?php echo $group->id?>'});" />
</div>
</form>

<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assigned roles');?> - <?php echo htmlspecialchars($group->name)?></h2>
<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" method="post">



<table class="lentele" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th>&nbsp;</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Name');?></th>
</tr>
</thead>
<?php foreach (erLhcoreClassGroupRole::getGroupRoles($group->id) as $UserAssigned) : ?>
<tr>
    <td><input type="checkbox" name="AssignedID[]" value="<?php echo $UserAssigned['assigned_id']?>" /></td>
    <td><?php echo htmlspecialchars($UserAssigned['name'])?></td>
</tr>
<?php endforeach; ?>
</table>



<input type="submit" class="small alert button" name="Remove_role_from_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Remove role from group');?>" /> 
<input class="small button" type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assign role');?>" onclick="$.colorbox({width:'850px',height:'600px', href:'<?php echo erLhcoreClassDesign::baseurl('permission/groupassignrole')?>/<?php echo $group->id?>'});" />


</form>
</fieldset>

<?php if (isset($adduser)) : ?>
<script type="text/javascript">
$(function() {
	$.colorbox({width:'850px',height:'600px',iframe:true,href:'<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser')?>/<?php echo $group->id?>'});
})
</script>
<?php endif; ?>