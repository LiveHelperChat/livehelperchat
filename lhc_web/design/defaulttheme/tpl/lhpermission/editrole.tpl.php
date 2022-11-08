<h1 ng-non-bindable><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Edit role');?> - <?php echo htmlspecialchars($role->name)?></h1>

    <?php if (isset($errors)) : ?>
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

	<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post" ng-non-bindable>
        
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<div class="form-group">
	       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></label>
	       <input class="form-control" type="text" name="Name" value="<?php echo htmlspecialchars($role->name);?>" />
	    </div>
	      
	    <div class="btn-group" role="group" aria-label="...">
			<input type="submit" class="btn btn-sm btn-secondary" name="Update_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Update');?>"/>
		    <input type="submit" class="btn btn-sm btn-secondary" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Cancel');?>"/>
		</div>
			
	    <hr>

		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assigned functions');?></h4>

        <div class="btn-group mb-2" role="group" aria-label="...">
            <input type="submit" class="btn btn-sm btn-warning" name="Delete_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected policy');?>"/>
            <input type="submit" class="btn btn-sm btn-secondary" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>" />
        </div>

		<table class="table table-sm">
		<thead>
		<tr>
		     <th width="1%">&nbsp;</th>
		     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module');?></th>
		     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function');?></th>
		     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Limitation');?></th>
		     <th width="1%">&nbsp;</th>
		</tr>
		</thead>
		     <?php foreach (erLhcoreClassRoleFunction::getRoleFunctions($role->id, '`module` ASC, `function` ASC') as $Function) : ?>
		     <tr>
			     <td><input type="checkbox" class="mb-0" name="PolicyID[]" value="<?php echo $Function['id']?>" /></td>
			     <?php include(erLhcoreClassDesign::designtpl('lhpermission/role_row.tpl.php'));?>
		     </tr>
		     <?php endforeach; ?>
		</table>
		
		<div class="btn-group" role="group" aria-label="...">
			 <input type="submit" class="btn btn-sm btn-warning" name="Delete_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected policy');?>"/>
		     <input type="submit" class="btn btn-sm btn-secondary" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>" />
		</div>
		
	</form>
<hr>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role assigned groups');?></h4>

<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post" ng-non-bindable>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
	<table class="table table-sm">
	<thead>
	<tr>
	     <th width="1%">&nbsp;</th>
	     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></th>
	</tr>
	</thead>
	     <?php foreach (erLhcoreClassGroupRole::getRoleGroups($role->id) as $Group) : ?>
	     <tr>
		     <td><input class="mb-0" type="checkbox" name="AssignedID[]" value="<?php echo $Group['assigned_id']?>" /></td>
		     <td><?php echo htmlspecialchars($Group['name'])?></td>
	     </tr>
	     <?php endforeach; ?>
	</table>
	<div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-sm btn-warning btn-secondary" name="Remove_group_from_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected role');?>"/>
	    <input type="button" class="btn btn-sm btn-secondary" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('permission/roleassigngroup')?>/<?php echo $role->id?>'});" name="Assign_group_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assign a group');?>"/>
	</div>
</form>



