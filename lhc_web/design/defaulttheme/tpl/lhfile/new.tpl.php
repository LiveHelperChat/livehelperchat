<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','New file');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>


<?php if (isset($file_uploaded) && $file_uploaded == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File uploaded'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if ($mode == 'reloadparent' && $file_uploaded == true) : ?>
<script>
setTimeout(function(){
	window.parent.location.reload();
},1500);
</script>
<?php endif;?>

<form action="" method="post" enctype="multipart/form-data">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File name');?></label>
	<input type="text" name="Name" />
	<input type="file" name="files" />
	<input type="submit" class="button small radius" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Upload');?>" />
</form>