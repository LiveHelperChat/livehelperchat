<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','New group');?></h1> 

<div class="articlebody">
<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/user/newgroup/')?>" method="post">
			<table>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Save_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save');?>"/> <input type="submit" class="default-button" name="Save_group_and_assign_user" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save and assign user');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>