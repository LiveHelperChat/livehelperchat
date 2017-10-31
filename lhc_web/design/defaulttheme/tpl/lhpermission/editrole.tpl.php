<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Edit role');?> - <?php echo $role->name?></h1>

    <?php if (isset($errors)) : ?>
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

	<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<div class="form-group">
	       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></label>
	       <input class="form-control" type="text" name="Name" value="<?php echo htmlspecialchars($role->name);?>" />
	    </div>
	      
	    <div class="btn-group" role="group" aria-label="...">
			<input type="submit" class="btn btn-default" name="Update_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Update');?>"/>
		    <input type="submit" class="btn btn-default" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Cancel');?>"/>
		</div>
			
	    <hr>

		<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assigned functions');?></h2>
		<table class="table">
		<thead>
		<tr>
		     <th width="1%">&nbsp;</th>
		     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module');?></th>
		     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function');?></th>
		     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Limitation');?></th>
		</tr>
		</thead>
		     <?php foreach (erLhcoreClassRoleFunction::getRoleFunctions($role->id) as $Function) : ?>
		     <tr>
			     <td><input type="checkbox" class="mb0" name="PolicyID[]" value="<?php echo $Function['id']?>" /></td>
			     <?php include(erLhcoreClassDesign::designtpl('lhpermission/role_row.tpl.php'));?>
		     </tr>
		     <?php endforeach; ?>
		</table>
		
		<div class="btn-group" role="group" aria-label="...">
			 <input type="submit" class="btn btn-default" name="Delete_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected policy');?>"/>
		     <input type="submit" class="btn btn-default" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>" />
		</div>
		
	</form>
<hr>

<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role assigned groups');?></h2>

<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
	<table class="table">
	<thead>
	<tr>
	     <th width="1%">&nbsp;</th>
	     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></th>
	</tr>
	</thead>
	     <?php foreach (erLhcoreClassGroupRole::getRoleGroups($role->id) as $Group) : ?>
	     <tr>
		     <td><input class="mb0" type="checkbox" name="AssignedID[]" value="<?php echo $Group['assigned_id']?>" /></td>
		     <td><?php echo htmlspecialchars($Group['name'])?></td>
	     </tr>
	     <?php endforeach; ?>
	</table>
	<div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-default" name="Remove_group_from_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected role');?>"/>
	    <input type="button" class="btn btn-default" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('permission/roleassigngroup')?>/<?php echo $role->id?>'});" name="Assign_group_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assign a group');?>"/>
	</div>
</form>



