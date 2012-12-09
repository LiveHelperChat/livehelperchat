<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit department');?> - <?php echo $departament->name?></h1> 
<div class="articlebody">
<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>
	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/departament/edit/'.$departament->id)?>" method="post">
			<table>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></td><td><input class="inputfield" type="text" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Update_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Update');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>