<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/new.tpl.php'));?></h1>

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
    <div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','File name');?></label>	
	   <input type="text" name="Name" class="form-control"/>
	</div>
	
	<div class="form-group">
	   <input type="file" name="files" />
	</div>
	
	<input type="submit" class="btn btn-default" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Upload');?>" />
</form>