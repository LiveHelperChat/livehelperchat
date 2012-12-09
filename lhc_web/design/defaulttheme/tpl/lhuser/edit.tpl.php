
<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit');?> - <?php echo $user->name,' ',$user->surname?></h1> 


<div class="articlebody">

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

<div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Do not enter password unless you want to change it');?></div>
	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/user/edit/'.$user->id)?>" method="post">
			<table>
				<tr><td colspan="2"><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Login information')?></strong></td></tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Username');?></td><td><input class="inputfield" type="text" name="" disabled value="<?php echo htmlspecialchars($user->username);?>" /></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password');?></td>
					<td><input type="password" class="inputfield" name="Password" value=""/></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Repeat password');?></td>
					<td><input type="password" class="inputfield" name="Password1" value=""/></td>
				</tr>
				<tr><td colspan="2"><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Contact information');?></strong></td></tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','E-mail');?></td>
					<td><input type="text" class="inputfield" name="Email" value="<?php echo $user->email;?>"/></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Name');?></td>
					<td><input type="text" class="inputfield" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Surname');?></td>
					<td><input type="text" class="inputfield" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/></td>
				</tr>					
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>
<br />
<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Assigned departments');?></legend>
<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : ?>
	<div class="dataupdate"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Account updated');?></div>
	<br />
<?php endif; ?>
<?php
    $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($user->id);
?>
<form action="<?php echo erLhcoreClassDesign::baseurl('/user/edit/'.$user->id)?>" method="post">
<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>"<?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?php echo $departament['name']?></label><br />
<?php endforeach; ?>
<br />
<input type="submit" class="default-button" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
</form>
</fieldset>


