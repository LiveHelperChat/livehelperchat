<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit');?> - <?php echo $user->name,' ',$user->surname?></h1> 

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Do not enter password unless you want to change it');?></div>
<br />

<form action="<?php echo erLhcoreClassDesign::baseurl('/user/edit/'.$user->id)?>" method="post" autocomplete="off">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Username');?></label>
<input class="inputfield" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password');?></label>
<input type="password" class="inputfield" name="Password" value=""/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Repeat password');?></label>
<input type="password" class="inputfield" name="Password1" value=""/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','E-mail');?></label>
<input type="text" class="inputfield" name="Email" value="<?php echo $user->email;?>"/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Name');?></label>
<input type="text" class="inputfield" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Surname');?></label>
<input type="text" class="inputfield" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'DefaultGroup[]',	                 
                    'selected_id'    => $user->user_groups_id,                      
					'multiple' 		 => true,                     
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
            )); ?>
            
<label>Disabled&nbsp;<input type="checkbox" value="on" name="UserDisabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : '' ?> /></label>  

<label>Do not show user status as online&nbsp;<input type="checkbox" value="on" name="HideMyStatus" <?php echo $user->hide_online == 1 ? 'checked="checked"' : '' ?> /></label>  
            
<ul class="button-group radius">
<li><input type="submit" class="small button" name="Save_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Save');?>"/></li>
<li><input type="submit" class="small button" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/></li>
<li><input type="submit" class="small button" name="Cancel_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Cancel');?>"/></li>
</ul>	

</form>
		
<hr>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Assigned departments');?></h5>

<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<?php $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($user->id); ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('/user/edit/'.$user->id)?>" method="post">
    
    <label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> />All departments</label>
    
    <?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
        <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>"<?php in_array($departament['id'],$userDepartaments) ? print 'checked="checked"' : '';?>/><?php echo htmlspecialchars($departament['name'])?></label>
    <?php endforeach; ?>
    <input type="submit" class="small button" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
</form>