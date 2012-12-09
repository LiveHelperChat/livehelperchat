<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user');?></h1> 

<div class="articlebody">

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/user/new/')?>" method="post">
			<table>
				<tr><td colspan="2"><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Login information')?></strong></td></tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Username');?></td><td><input class="inputfield" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" /></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Password');?></td>
					<td><input type="password" class="inputfield" name="Password" value=""/></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Repeat password');?></td>
					<td><input type="password" class="inputfield" name="Password1" value=""/></td>
				</tr>
				<tr><td colspan="2"><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Contact information');?></strong></td></tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','E-mail');?></td>
					<td><input type="text" class="inputfield" name="Email" value="<?php echo $user->email;?>"/></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Name');?></td>
					<td><input type="text" class="inputfield" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/></td>
				</tr>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Surname');?></td>
					<td><input type="text" class="inputfield" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/></td>
				</tr>
				<tr>
				    <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Assigned departments');?></td>
				    <td><?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
                            <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>"<?php echo in_array($departament['id'],$userdepartaments) ? 'checked="checked"' : '';?>/> <?php echo $departament['name']?></label><br />
                        <?php endforeach; ?>
                   </td>
				</tr>					
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>"/></td>
				</tr>
			</table>	
			
		</form>
	</div>
</div>
