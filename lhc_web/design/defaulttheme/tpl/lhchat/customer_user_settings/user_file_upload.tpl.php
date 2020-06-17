<?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/user_file_upload_pre.tpl.php'));?>

<?php if ($user_file_upload_enabled == true) : ?>

<?php
    $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

    $chatUploadEnabled = false;

    if (isset($chat)) {
        $chatVariables = $chat->chat_variables_array;
        if (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1) {
            $chatUploadEnabled = true;
        }
    }
?>
<?php if ((isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || $chatUploadEnabled == true) : ?>
<a class="file-uploader" href="#"><i class="material-icons chat-setting-item text-muted">attach_file</i>
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]" multiple>
</a>
<?php endif;?>

<?php endif;?>