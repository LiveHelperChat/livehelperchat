<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role edit');?> - <?php echo $role->name?></h1>

    <?php if (isset($errors)) : ?>
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>


		<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
	
		    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></label>
		    <input  type="text" name="Name" value="<?php echo htmlspecialchars($role->name);?>" />
		      
			<ul class="button-group radius">
			<li><input type="submit" class="small button" name="Update_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Update');?>"/></li>
			<li><input type="submit" class="small button" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Cancel');?>"/></li>
			</ul>
		    <hr>

			<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assigned functions');?></h2>
			<table cellpadding="0" cellspacing="0">
			<thead>
			<tr>
			     <th>&nbsp;</th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module');?></th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function');?></th>
			</tr>
			</thead>
			     <?php foreach (erLhcoreClassRoleFunction::getRoleFunctions($role->id) as $Function) : ?>
			     <tr>
    			     <td><input type="checkbox" class="mb0" name="PolicyID[]" value="<?php echo $Function['id']?>" /></td>
    			     <td><?php echo htmlspecialchars(erLhcoreClassModules::getModuleName($Function['module']))?>&nbsp;(<b><?php echo htmlspecialchars($Function['module'])?></b>)</td>
    			     <td><?php echo htmlspecialchars(erLhcoreClassModules::getFunctionName($Function['module'],$Function['function']))?>&nbsp;(<b><?php echo htmlspecialchars($Function['function'])?></b>)</td>
			     </tr>
			     <?php endforeach; ?>
			</table>
			<ul class="button-group radius">
			<li><input type="submit" class="small button" name="Delete_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected policy');?>"/></li>
			<li><input type="submit" class="small button" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>"/></li>
            </ul>
		</form>
<hr>

<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role assigned groups');?></h2>

		<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
			<table cellpadding="0" cellspacing="0">
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
			<ul class="button-group radius">
            <li><input type="submit" class="small button" name="Remove_group_from_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected role');?>"/></li>
			<li><input type="button" class="small button" onclick="lhinst.revealModal('<?php echo erLhcoreClassDesign::baseurl('permission/roleassigngroup')?>/<?php echo $role->id?>');" name="Assign_group_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assign a group');?>"/></li>
			</ul>
		</form>



