<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New role');?></h1>

<div class="articlebody">

<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

	<div><br />
		<form action="<?php echo erLhcoreClassDesign::baseurl('/permission/newrole/')?>" method="post">
			<table>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Title');?></td><td><input class="inputfield" type="text" name="Name"  value="" /></td>
				</tr>
			</table>
			<br />
			<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Policy list');?></legend> 			
			<table class="lentele" cellpadding="0" cellspacing="0">
			<tr>
			     <th>&nbsp;</th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Module');?></th>
			     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Function');?></th>	
			</tr>			     
			</table>			<br />
			<input type="submit" class="default-button" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New policy');?>"/>
			
			</fieldset>
			<br />
			<input type="submit" class="default-button" name="Save_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Save');?>"/>	
			<input type="submit" class="default-button" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Cancel');?>"/>	
		</form>
	</div>
</div>