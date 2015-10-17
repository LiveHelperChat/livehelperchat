<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('department/new')?>" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhdepartment/form.tpl.php'));?>
	
    <div class="btn-group" role="group" aria-label="...">
		<input type="submit" class="btn btn-default" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
		<input type="submit" class="btn btn-default" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
	</div>
	
</form>
