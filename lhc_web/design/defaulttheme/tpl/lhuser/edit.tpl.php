
<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit');?> - <? echo $user->name,' ',$user->surname?></legend> 


<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

<div class="explain"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Do not enter password unless you want to change it');?></div>
	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/user/edit/'.$user->id)?>" method="post">
			<table>
				<tr><td colspan="2"><strong><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Login information')?></strong></td></tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Username');?></td><td><input class="inputfield" type="text" name="" disabled value="<?=htmlspecialchars($user->username);?>" /></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password');?></td>
					<td><input type="password" class="inputfield" name="Password" value=""/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Repeat password');?></td>
					<td><input type="password" class="inputfield" name="Password1" value=""/></td>
				</tr>
				<tr><td colspan="2"><strong><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Contact information');?></strong></td></tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','E-mail');?></td>
					<td><input type="text" class="inputfield" name="Email" value="<?=$user->email;?>"/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Name');?></td>
					<td><input type="text" class="inputfield" name="Name" value="<?=htmlspecialchars($user->name);?>"/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Surname');?></td>
					<td><input type="text" class="inputfield" name="Surname" value="<?=htmlspecialchars($user->surname);?>"/></td>
				</tr>					
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_account" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>
<br />
<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Assigned departments');?></legend>
<? if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : ?>
	<div class="dataupdate"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Account updated');?></div>
	<br />
<? endif; ?>
<?
    $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($user->id);
?>
<form action="<?=erLhcoreClassDesign::baseurl('/user/edit/'.$user->id)?>" method="post">
<? foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" name="UserDepartament[]" value="<?=$departament['id']?>"<?=in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?=$departament['name']?></label><br />
<? endforeach; ?>
<br />
<input type="submit" class="default-button" name="UpdateDepartaments_account" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Update');?>"/>
</form>
</fieldset>

</fieldset>
