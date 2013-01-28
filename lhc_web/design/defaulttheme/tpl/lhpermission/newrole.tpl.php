<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New role');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('permission/newrole')?>" method="post">

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Title');?></label>
    <input class="inputfield" type="text" name="Name"  value="" />

	
	
	<fieldset>
	<legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Policy list');?></legend> 		
	
	<table class="lentele" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	     <th>&nbsp;</th>
	     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Module');?></th>
	     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Function');?></th>	
	</tr>	
	</thead>		     
	</table>
	<input type="submit" class="small button" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New policy');?>"/>
	<br /><br />

	
	</fieldset>
	
	<ul class="button-group radius">
	<li><input type="submit" class="small button" name="Save_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Save');?>"/></li>
	<li><input type="submit" class="small button" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Cancel');?>"/></li>
	</ul>	
</form>
