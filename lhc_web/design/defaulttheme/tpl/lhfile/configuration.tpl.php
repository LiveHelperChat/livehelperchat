<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/configuration.tpl.php'));?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="ActiveFileUploadUser" value="on" <?php isset($file_data['active_user_upload']) && ($file_data['active_user_upload'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for users is active'); ?></label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="ActiveFileUploadAdmin" value="on" <?php isset($file_data['active_admin_upload']) && ($file_data['active_admin_upload'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for operators is active'); ?></label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="removeMetaTag" value="on" <?php isset($file_data['remove_meta']) && ($file_data['remove_meta'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Remove meta information from images'); ?></label>
            </div>
        </div>
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

    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','ClamAV antivirus configuration'); ?></h3>

    <div class="form-group">
        <label><input type="checkbox" name="AntivirusFileScanEnabled" value="on" <?php isset($file_data['clamav_enabled']) && ($file_data['clamav_enabled'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Uploaded files from visitors are scanned'); ?></label>
    </div>

    <div class="row form-group">
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','ClamAV socket path'); ?></label>
            <input type="text" class="form-control" name="ClamAVSocketPath" value="<?php isset($file_data['clamd_sock']) ? print $file_data['clamd_sock'] : print '/var/run/clamav/clamd.sock' ?>" />
        </div>
        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','ClamAV socket length'); ?></label>
            <input type="text" class="form-control" name="ClamAVSocketLength" value="<?php isset($file_data['clamd_sock_len']) ? print $file_data['clamd_sock_len'] : print '20000' ?>" />
        </div>
    </div>

    <hr>

    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Voice messages'); ?></h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>
                    <input type="checkbox" name="soundMessages" value="on" <?php isset($file_data['sound_messages']) && ($file_data['sound_messages'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Voice messages for visitors is active'); ?>
                </label>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>
                    <input type="checkbox" name="soundMessagesOp" value="on" <?php isset($file_data['sound_messages_op']) && ($file_data['sound_messages_op'] == true) ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Voice messages for operators is active'); ?>
                </label>
            </div>
        </div>

        <div class="col-md-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Voice message max length in seconds'); ?></label>
            <input type="text" class="form-control" name="soundLength" value="<?php isset($file_data['sound_length']) ? print $file_data['sound_length'] : print '30' ?>" />
        </div>
    </div>

    <hr>

    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Maintenance'); ?></h3>

    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Files will be deleted only if days is > 0')?></p>

    <div class="row">
        <div class="col-4">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Delete files'); ?></p>
            <label><input type="checkbox" name="typeDelete[]" <?php if (isset($file_data['mtype_delete']) && in_array('visitors',$file_data['mtype_delete'])) : ?>checked="checked"<?php endif;?> value="visitors">Visitors</label><br/>
            <label><input type="checkbox" name="typeDelete[]" <?php if (isset($file_data['mtype_delete']) && in_array('operators',$file_data['mtype_delete'])) : ?>checked="checked"<?php endif;?> value="operators">Operators</label>
        </div>
        <div class="col-4">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','if file is (OR)'); ?></p>
            <label><input type="checkbox" name="typeChatDelete[]" <?php if (isset($file_data['mtype_cdelete']) && in_array('unassigned',$file_data['mtype_cdelete'])) : ?>checked="checked"<?php endif;?> value="unassigned">Unassigned to chat</label><br/>
            <label><input type="checkbox" name="typeChatDelete[]" <?php if (isset($file_data['mtype_cdelete']) && in_array('assigned',$file_data['mtype_cdelete'])) : ?>checked="checked"<?php endif;?> value="assigned">Assigned to chat</label><br/>
        </div>
        <div class="col-4">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','And operator file is older than n days'); ?></p>
            <div class="form-group">
                <input type="text" class="form-control" name="mdays_older" value="<?php if (isset($file_data['mdays_older'])) : ?><?php echo htmlspecialchars($file_data['mdays_older'])?><?php endif?>" />
            </div>

            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','And visitor file is older than n days'); ?></p>
            <div class="form-group">
                <input type="text" class="form-control" name="mdays_older_visitor" value="<?php if (isset($file_data['mdays_older_visitor'])) : ?><?php echo htmlspecialchars($file_data['mdays_older_visitor'])?><?php endif?>" />
            </div>
        </div>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreFileConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>


