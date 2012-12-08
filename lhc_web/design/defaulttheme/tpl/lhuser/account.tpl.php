<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Logged user');?> - <? echo $user->name,' ',$user->surname?></legend>

<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

<? if (isset($account_updated) && $account_updated == 'done') : ?>
	<div class="dataupdate"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated');?></div>
<? endif; ?>


<div class="explain"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not enter password unless you want to change it');?></div>
	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/user/account/')?>" method="post">
			<table>
				<tr><td colspan="2"><strong><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Login information')?></strong></td></tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Username');?></td><td><input class="inputfield" type="text" name="" disabled value="<?=htmlspecialchars($user->username);?>" /></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Password');?></td>
					<td><input type="password" class="inputfield" name="Password" value=""/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Repeat password');?></td>
					<td><input type="password" class="inputfield" name="Password1" value=""/></td>
				</tr>
				<tr><td colspan="2"><strong><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Contact information');?></strong></td></tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','E-mail');?></td>
					<td><input type="text" class="inputfield" name="Email" value="<?=$user->email;?>"/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Name');?></td>
					<td><input type="text" class="inputfield" name="Name" value="<?=htmlspecialchars($user->name);?>"/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Surname');?></td>
					<td><input type="text" class="inputfield" name="Surname" value="<?=htmlspecialchars($user->surname);?>"/></td>
				</tr>					
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_account" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>
<br />
<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></legend>
<? if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : ?>
	<div class="dataupdate"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated');?></div>
	<br />
<? endif; ?>

<? if ($alldepartaments === true) :?>
<div class="dataupdate"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','You have global right to see all departments chats');?></div>
<br />
<? endif;?>

<?
    $userDepartaments = erLhcoreClassUserDep::getUserDepartaments();
?>
<? if ($editdepartaments === true) { ?>
<form action="<?=erLhcoreClassDesign::baseurl('/user/account/')?>" method="post">
<? foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" name="UserDepartament[]" value="<?=$departament['id']?>"<?=in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?=$departament['name']?></label><br />
<? endforeach; ?>
<br />
<input type="submit" class="default-button" name="UpdateDepartaments_account" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
</form>
<? } else {?>
<? foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" disabled value="<?=$departament['id']?>"<?=in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?=$departament['name']?></label><br />
<? endforeach; ?>

<? } ?>
</fieldset>

</fieldset>