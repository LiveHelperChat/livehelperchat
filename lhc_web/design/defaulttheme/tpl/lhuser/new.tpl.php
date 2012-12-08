<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user');?></legend> 



<div class="articlebody">

<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

	<div><br />
		<form action="<?=erLhcoreClassDesign::baseurl('/user/new/')?>" method="post">
			<table>
				<tr><td colspan="2"><strong><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Login information')?></strong></td></tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Username');?></td><td><input class="inputfield" type="text" name="Username" value="<?=htmlspecialchars($user->username);?>" /></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Password');?></td>
					<td><input type="password" class="inputfield" name="Password" value=""/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Repeat password');?></td>
					<td><input type="password" class="inputfield" name="Password1" value=""/></td>
				</tr>
				<tr><td colspan="2"><strong><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Contact information');?></strong></td></tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','E-mail');?></td>
					<td><input type="text" class="inputfield" name="Email" value="<?=$user->email;?>"/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Name');?></td>
					<td><input type="text" class="inputfield" name="Name" value="<?=htmlspecialchars($user->name);?>"/></td>
				</tr>
				<tr>
					<td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Surname');?></td>
					<td><input type="text" class="inputfield" name="Surname" value="<?=htmlspecialchars($user->surname);?>"/></td>
				</tr>
				<tr>
				    <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Assigned departments');?></td>
				    <td><? foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
                            <label><input type="checkbox" name="UserDepartament[]" value="<?=$departament['id']?>"<?=in_array($departament['id'],$userdepartaments) ? 'checked="checked"' : '';?>/> <?=$departament['name']?></label><br />
                        <? endforeach; ?>
                   </td>
				</tr>					
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_account" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>"/></td>
				</tr>
			</table>	
			
		</form>
	</div>
</div>

</fieldset>
