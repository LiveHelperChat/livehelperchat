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

    <div class="form-group">
        <label><input type="checkbox" name="persistent" value="on" <?php if (isset($persistent) && $persistent == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Persistent');?></label>
        <span class="d-block text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Files maintenance jobs will not be run on this file.');?></span>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<input type="submit" class="btn btn-secondary" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Upload');?>" />
</form>