<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Import theme');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Theme imported'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off" enctype="multipart/form-data">

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','File')?> (json)</label>
	<input type="file" name="themefile" value="" />
	
	<input type="submit" name="ImportTheme" class="button small radius" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Import')?>" />
	
</form>
	