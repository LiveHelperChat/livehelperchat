<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role edit');?> - <?php echo $role->name?></h1>
    
    <?php if (isset($errors)) : ?>
    		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

	
		<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">
		      <div class="row">
		          <div class="columns two">
		              <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></label>
		          </div>
		          <div class="columns ten end">
		              <label><input class="inputfield" type="text" name="Name"  value="<?php echo htmlspecialchars($role->name);?>" /></label>
		          </div>		      
		      </div>
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
    			     <td><input type="checkbox" name="PolicyID[]" value="<?php echo $Function['id']?>" /></td>
    			     <td><?php echo $Function['module']?></td>  
    			     <td><?php echo $Function['function']?></td>  	
			     </tr>			     
			     <?php endforeach; ?>	
			</table>	
			<ul class="button-group radius">		
			<li><input type="submit" class="small button" name="Delete_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected');?>"/></li>
			<li><input type="submit" class="small button" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','New policy');?>"/></li>	
            </ul>
		</form>		
<hr>

<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role assigned groups');?></h2>

		<form action="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" method="post">			
			<table cellpadding="0" cellspacing="0">
			<thead>
			<tr>
			     <th width="1%">&nbsp;</th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Title');?></th>	
			</tr>
			</thead>
			     <?php foreach (erLhcoreClassGroupRole::getRoleGroups($role->id) as $Group) : ?>
			     <tr>			     
    			     <td><input type="checkbox" name="AssignedID[]" value="<?php echo $Group['assigned_id']?>" /></td>
    			     <td><?php echo htmlspecialchars($Group['name'])?></td>      		
			     </tr>			     
			     <?php endforeach; ?>	
			</table>	
			<ul class="button-group radius">
            <li><input type="submit" class="small button" name="Remove_group_from_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Remove selected');?>"/></li>			
			<li><input type="button" class="small button" onclick="$.colorbox({width:'600px',href:'<?php echo erLhcoreClassDesign::baseurl('permission/roleassigngroup')?>/<?php echo $role->id?>'});" name="Assign_group_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Assign group');?>"/></li>
			</ul>
		</form>
	


