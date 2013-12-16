<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','File configuration');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<label><input type="checkbox" name="ActiveFileUploadUser" value="on" <?php isset($file_data['active_user_upload']) && ($file_data['active_user_upload'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for users is active'); ?></label>
<label><input type="checkbox" name="ActiveFileUploadAdmin" value="on" <?php isset($file_data['active_admin_upload']) && ($file_data['active_admin_upload'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for operators is active'); ?></label>

<div class="row">
	<div class="columns large-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Allowed files types for operators'); ?></label>
		<input type="text" name="AllowedFileTypes" value="<?php isset($file_data['ft_op']) ? print $file_data['ft_op'] : '' ?>" />
	</div>
	<div class="columns large-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Allowed files types for users'); ?></label>
		<input type="text" name="AllowedFileTypesUser" value="<?php isset($file_data['ft_us']) ? print $file_data['ft_us'] : '' ?>" />
	</div>
</div>

<div class="row">
	<div class="columns large-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Maximum file size in KB'); ?></label>
		<input type="text" name="MaximumFileSize" value="<?php isset($file_data['fs_max']) ? print $file_data['fs_max'] : '' ?>" />
	</div>
</div>

<input type="submit" class="button small round" name="StoreFileConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>


