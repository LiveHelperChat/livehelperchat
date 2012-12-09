<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role edit');?> - <?php echo $role->name?></h1>

<div class="articlebody">

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/permission/editrole/'.$role->id)?>" method="post">
			<table>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="<?php echo htmlspecialchars($role->name);?>" /></td>
				</tr>
			</table>
			<br />
			<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assigned functions');?></legend> 			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <th>&nbsp;</th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module');?></th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function');?></th>	
			</tr>
			     <?php foreach (erLhcoreClassRoleFunction::getRoleFunctions($role->id) as $Function) : ?>
			     <tr>			     
    			     <td><input type="checkbox" name="PolicyID[]" value="<?php echo $Function['id']?>" /></td>
    			     <td><?php echo $Function['module']?></td>  
    			     <td><?php echo $Function['function']?></td>  	
			     </tr>			     
			     <?php endforeach; ?>	
			</table>			<br />
			<input type="submit" class="default-button" name="Delete_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected');?>"/>
			<input type="submit" class="default-button" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>"/>			
			</fieldset>
			<br />
			<input type="submit" class="default-button" name="Update_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Update');?>"/>	
			<input type="submit" class="default-button" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Cancel');?>"/>	
		</form>
	</div>
</div>

<br />
<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role assigned groups');?></legend>
	<div>
		<form action="<?php echo erLhcoreClassDesign::baseurl('/permission/editrole/'.$role->id)?>" method="post">			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <th width="1%">&nbsp;</th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></th>	
			</tr>
			     <?php foreach (erLhcoreClassGroupRole::getRoleGroups($role->id) as $Group) : ?>
			     <tr>			     
    			     <td><input type="checkbox" name="AssignedID[]" value="<?php echo $Group['assigned_id']?>" /></td>
    			     <td><?php echo $Group['name']?></td>      		
			     </tr>			     
			     <?php endforeach; ?>	
			</table>	
			<br />

			<input type="submit" class="default-button" name="Remove_group_from_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected');?>"/>	
			<input type="button" class="default-button" onclick="lhinst.abstractDialog('assign-group-dialog','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Group assignement to role');?>','<?php echo erLhcoreClassDesign::baseurl('permission/roleassigngroup/'.$role->id)?>')" name="Assign_group_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assign group');?>"/>	
		</form>
	</div>
</fieldset>
<div id="assign-group-dialog"></div>