<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role edit');?> - <?=$role->name?></legend>

<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/permission/editrole/'.$role->id)?>" method="post">
			<table>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="<?=htmlspecialchars($role->name);?>" /></td>
				</tr>
			</table>
			<br />
			<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assigned functions');?></legend> 			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <th>&nbsp;</th>
			     <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module');?></th>
			     <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function');?></th>	
			</tr>
			     <? foreach (erLhcoreClassRoleFunction::getRoleFunctions($role->id) as $Function) : ?>
			     <tr>			     
    			     <td><input type="checkbox" name="PolicyID[]" value="<?=$Function['id']?>" /></td>
    			     <td><?=$Function['module']?></td>  
    			     <td><?=$Function['function']?></td>  	
			     </tr>			     
			     <? endforeach; ?>	
			</table>			<br />
			<input type="submit" class="default-button" name="Delete_policy" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected');?>"/>
			<input type="submit" class="default-button" name="New_policy" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>"/>			
			</fieldset>
			<br />
			<input type="submit" class="default-button" name="Update_role" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Update');?>"/>	
			<input type="submit" class="default-button" name="Cancel_role" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Cancel');?>"/>	
		</form>
	</div>
</div>
</fieldset>
<br />
<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role assigned groups');?></legend>
	<div>
		<form action="<?=erLhcoreClassDesign::baseurl('/permission/editrole/'.$role->id)?>" method="post">			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <th width="1%">&nbsp;</th>
			     <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></th>	
			</tr>
			     <? foreach (erLhcoreClassGroupRole::getRoleGroups($role->id) as $Group) : ?>
			     <tr>			     
    			     <td><input type="checkbox" name="AssignedID[]" value="<?=$Group['assigned_id']?>" /></td>
    			     <td><?=$Group['name']?></td>      		
			     </tr>			     
			     <? endforeach; ?>	
			</table>	
			<br />

			<input type="submit" class="default-button" name="Remove_group_from_role" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected');?>"/>	
			<input type="button" class="default-button" onclick="lhinst.abstractDialog('assign-group-dialog','<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Group assignement to role');?>','<?=erLhcoreClassDesign::baseurl('permission/roleassigngroup/'.$role->id)?>')" name="Assign_group_role" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assign group');?>"/>	
		</form>
	</div>
</fieldset>
<div id="assign-group-dialog"></div>