<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','File configuration');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">
<div class="form-group">
    <label><input type="checkbox" name="ActiveFileUploadUser" value="on" <?php isset($file_data['active_user_upload']) && ($file_data['active_user_upload'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for users is active'); ?></label>
</div>

<div class="form-group">
    <label><input type="checkbox" name="ActiveFileUploadAdmin" value="on" <?php isset($file_data['active_admin_upload']) && ($file_data['active_admin_upload'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for operators is active'); ?></label>
</div>

<div class="row form-group">
	<div class="col-md-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Allowed files types for operators'); ?></label>
		<input type="text" class="form-control" name="AllowedFileTypes" value="<?php isset($file_data['ft_op']) ? print $file_data['ft_op'] : '' ?>" />
	</div>
	<div class="col-md-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Allowed files types for users'); ?></label>
		<input type="text" class="form-control" name="AllowedFileTypesUser" value="<?php isset($file_data['ft_us']) ? print $file_data['ft_us'] : '' ?>" />
	</div>
</div>

<div class="row form-group">
	<div class="col-md-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Maximum file size in KB'); ?></label>
		<input type="text" class="form-control" name="MaximumFileSize" value="<?php isset($file_data['fs_max']) ? print $file_data['fs_max'] : '' ?>" />
	</div>
</div>

<input type="submit" class="btn btn-default" name="StoreFileConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>


