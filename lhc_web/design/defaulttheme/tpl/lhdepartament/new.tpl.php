<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department');?></h1> 
<div class="articlebody">

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/departament/new/')?>" method="post">
			<table>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Name');?></td><td><input class="inputfield" type="text" name="Name"  value="" /></td>
				</tr>									
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="default-button" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Save');?>"/></td>
				</tr>
			</table>		
		</form>
	</div>
</div>