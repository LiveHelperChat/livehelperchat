<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Group edit');?> - <?php echo htmlspecialchars($group->name)?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div>
	<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" method="post">

		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Title');?></label>
		  <input class="form-control" type="text" name="Name"  value="<?php echo htmlspecialchars($group->name);?>" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Member of this group can work with the following groups');?></label>
            <select multiple="multiple" size="5" class="form-control" name="MemberGroup[]">
                <?php 
                
                $assignedGrupsIds = array();
                foreach ($group_work as $groupId) {
                    $assignedGrupsIds[] = $groupId->group_work_id;
                }
                
                foreach (erLhcoreClassModelGroup::getList() as $groupMember) : ?>
                    <option value="<?php echo $groupMember->id?>"<?php in_array($groupMember->id, $assignedGrupsIds) ? print ' selected="selected" ' : ''?>><?php echo htmlspecialchars($groupMember->name)?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Disabled');?> <input type="checkbox" name="Disabled"  value="on" <?php echo $group->disabled == 1 ? 'checked="checked"' : ''?> /></label>
        </div>
        
		<input type="submit" class="btn btn-default" name="Update_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Update');?>"/>

	</form>
</div>


<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assigned users');?> - <?php echo htmlspecialchars($group->name)?></h4>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">&nbsp;</th>
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


<input type="submit" class="btn btn-danger" name="Remove_user_from_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Remove user from the group');?>" /> 
<input class="btn btn-default" type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assign user');?>" onclick="lhc.revealModal({'iframe':true,'height':600,'url':'<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser')?>/<?php echo $group->id?>'})" />

</form>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assigned roles');?> - <?php echo htmlspecialchars($group->name)?></h4>
<form action="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">&nbsp;</th>
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

<input type="submit" class="btn btn-danger" name="Remove_role_from_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Remove role from group');?>" />
<input class="btn btn-default" type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Assign role');?>" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('permission/groupassignrole')?>/<?php echo $group->id?>'});" />

</form>

<?php if (isset($adduser)) : ?>
<script type="text/javascript">
$(function() {
	lhc.revealModal({'iframe':true,'height':600,'url':'<?php echo erLhcoreClassDesign::baseurl('user/groupassignuser')?>/<?php echo $group->id?>'});	
})
</script>
<?php endif; ?>