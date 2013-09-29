<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit department');?> - <?php echo htmlspecialchars($departament->name)?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('departament/edit')?>/<?php echo $departament->id?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhdepartament/form.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
      <li><input type="submit" class="small button" name="Update_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
      <li><input type="submit" class="small alert button" name="Delete_departament" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Are you sure?');?>')" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete department');?>"/></li>
    </ul>

</form>
