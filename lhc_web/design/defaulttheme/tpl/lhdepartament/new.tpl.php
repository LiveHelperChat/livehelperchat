<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('departament/new')?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhdepartament/form.tpl.php'));?>

    <ul class="button-group radius">
    	<li><input type="submit" class="small button" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
		<li><input type="submit" class="small button" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
	</ul>

</form>
