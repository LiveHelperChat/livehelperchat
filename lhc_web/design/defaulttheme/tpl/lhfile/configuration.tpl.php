<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/configuration.tpl.php'));?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" ng-non-bindable method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="ActiveFileUploadUser" value="on" <?php isset($file_data['active_user_upload']) && ($file_data['active_user_upload'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for users is active'); ?></label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="AllowOnlyOneFileUpload" value="on" <?php isset($file_data['one_file_upload']) && ($file_data['one_file_upload'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Allow visitor to choose only one file for the upload'); ?></label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="ActiveFileUploadAdmin" value="on" <?php isset($file_data['active_admin_upload']) && ($file_data['active_admin_upload'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Files upload for operators is active'); ?></label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><input type="checkbox" name="removeMetaTag" value="on" <?php isset($file_data['remove_meta']) && ($file_data['remove_meta'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Remove meta information from images'); ?></label>
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
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Maximum file size in KB'); ?>
            <span class="badge bg-info me-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Max file size'); ?> - <?php echo ini_get('upload_max_filesize')?></span><span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Max post size'); ?> - <?php echo ini_get('post_max_size')?></span>
        </label>
		<input type="text" class="form-control" name="MaximumFileSize" value="<?php isset($file_data['fs_max']) ? print $file_data['fs_max'] : '' ?>" />
	</div>
    <div class="col-md-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Maximum visitor image resolution. Images will be resized to fit within a square of this many pixels.'); ?></label>
		<input type="number" class="form-control" placeholder="1024" name="MaximumResolution" value="<?php isset($file_data['max_res']) && (int)$file_data['max_res'] > 10 ? print (int)$file_data['max_res'] : '' ?>" />
	</div>
</div>

    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Files download permissions'); ?></h3>
    <div class="col-md-12">
        <div class="row">
            <div class="col-6 mt-2">
                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Chat related file can be downloaded by visitors'); ?></h6>
                <label class="d-block"><input type="radio" name="chat_file_policy_v" <?php if (!isset($file_data['chat_file_policy_v']) || $file_data['chat_file_policy_v'] == 0) : ?>checked="checked"<?php endif;?> value="0"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Always (default)'); ?></label>
                <label class="d-block"><input type="radio" name="chat_file_policy_v" <?php if (isset($file_data['chat_file_policy_v']) && $file_data['chat_file_policy_v'] == 1) : ?>checked="checked"<?php endif;?> value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','If chat is in pending/active/bot status'); ?></label>
                <span class="text-muted d-block fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','For 10 minutes after chat being closed visitor will be able to download a files'); ?></span>


                <h6 class="mt-4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Chat related file can be downloaded by operator'); ?></h6>
                <label class="d-block"><input type="radio" name="chat_file_policy_o" <?php if (!isset($file_data['chat_file_policy_o']) || $file_data['chat_file_policy_o'] == 0) : ?>checked="checked"<?php endif;?> value="0"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Always (default)'); ?></label>
                <label class="d-block"><input type="radio" name="chat_file_policy_o" <?php if (isset($file_data['chat_file_policy_o']) && $file_data['chat_file_policy_o'] == 1) : ?>checked="checked"<?php endif;?> value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Has permission to related chat'); ?></label>
                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Image file by operator can be downloaded if'); ?> <a target="_blank" href="https://doc.livehelperchat.com/docs/bot/image-file-verification-flow"><span class="material-icons">help</span></a></h6>
                <select class="form-control form-control-sm" name="img_download_policy">
                    <option value="0" <?php if (!isset($file_data['img_download_policy']) || $file_data['img_download_policy'] == 0) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Default, all images files will be downloaded'); ?></option>
                    <option value="1" <?php if (isset($file_data['img_download_policy']) && $file_data['img_download_policy'] == 1) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Verified and has permission to download protected images OR has permission to download unprotected files'); ?></option>
                </select>
                <label class="d-block"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Minimum image width or height to require verification'); ?></label>
                <input type="number" class="form-control" min="10" placeholder="100" name="img_verify_min_dim" value="<?php isset($file_data['img_verify_min_dim']) && (int)$file_data['img_verify_min_dim'] > 10 ? print (int)$file_data['img_verify_min_dim'] : '' ?>" />


            </div>
            <div class="col-6 mt-2">
                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Mail related file can be downloaded by operator if he'); ?></h6>
                <label class="d-block"><input type="radio" name="mail_file_policy" <?php if (!isset($file_data['mail_file_policy']) || $file_data['mail_file_policy'] == 0) : ?>checked="checked"<?php endif;?> value="0" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Has permission to use mail module (default)'); ?></label>
                <label class="d-block"><input type="radio" name="mail_file_policy" <?php if (isset($file_data['mail_file_policy']) && $file_data['mail_file_policy'] == 1) : ?>checked="checked"<?php endif;?> value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Has permission to use mail module and access mail.'); ?></label>

                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Image file by operator can be downloaded if'); ?> <a target="_blank" href="https://doc.livehelperchat.com/docs/bot/image-file-verification-flow"><span class="material-icons">help</span></a></h6>
                <select class="form-control form-control-sm" name="mail_img_download_policy">
                    <option value="0" <?php if (!isset($file_data['mail_img_download_policy']) || $file_data['mail_img_download_policy'] == 0) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Default, all images files will be downloaded'); ?></option>
                    <option value="1" <?php if (isset($file_data['mail_img_download_policy']) && $file_data['mail_img_download_policy'] == 1) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Verified and has permission to download protected images OR has permission to download unprotected files'); ?></option>
                </select>
                <label class="d-block"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Minimum image width or height to require verification'); ?></label>
                <input type="number" class="form-control" min="10" placeholder="100" name="mail_img_verify_min_dim" value="<?php isset($file_data['mail_img_verify_min_dim']) && (int)$file_data['mail_img_verify_min_dim'] > 10 ? print (int)$file_data['mail_img_verify_min_dim'] : '' ?>" />

            </div>

        </div>
    </div>

    <h3 class="mt-4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','ClamAV antivirus configuration'); ?></h3>

    <div class="form-group">
        <label><input type="checkbox" name="AntivirusFileScanEnabled" value="on" <?php isset($file_data['clamav_enabled']) && ($file_data['clamav_enabled'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Uploaded files from visitors are scanned'); ?></label>
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
                    <input type="checkbox" name="soundMessages" value="on" <?php isset($file_data['sound_messages']) && ($file_data['sound_messages'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Voice messages for visitors is active'); ?>
                </label>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>
                    <input type="checkbox" name="soundMessagesOp" value="on" <?php isset($file_data['sound_messages_op']) && ($file_data['sound_messages_op'] == true) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Voice messages for operators is active'); ?>
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
            <label><input type="checkbox" name="typeDelete[]" <?php if (isset($file_data['mtype_delete']) && in_array('visitors',$file_data['mtype_delete'])) : ?>checked="checked"<?php endif;?> value="visitors"> Visitors</label><br/>
            <label><input type="checkbox" name="typeDelete[]" <?php if (isset($file_data['mtype_delete']) && in_array('operators',$file_data['mtype_delete'])) : ?>checked="checked"<?php endif;?> value="operators"> Operators</label>
        </div>
        <div class="col-4">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','if file is (OR)'); ?></p>
            <label><input type="checkbox" name="typeChatDelete[]" <?php if (isset($file_data['mtype_cdelete']) && in_array('unassigned',$file_data['mtype_cdelete'])) : ?>checked="checked"<?php endif;?> value="unassigned"> Unassigned to chat</label><br/>
            <label><input type="checkbox" name="typeChatDelete[]" <?php if (isset($file_data['mtype_cdelete']) && in_array('assigned',$file_data['mtype_cdelete'])) : ?>checked="checked"<?php endif;?> value="assigned"> Assigned to chat</label><br/>
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


