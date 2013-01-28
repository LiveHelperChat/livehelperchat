<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit department');?> - <?php echo $departament->name?></h1> 

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('/departament/edit/'.$departament->id)?>" method="post">

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input class="inputfield" type="text" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" />

	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Save');?>"/></li>
      <li><input type="submit" class="small button" name="Update_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Update');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Cancel');?>"/></li>
    </ul>

</form>
