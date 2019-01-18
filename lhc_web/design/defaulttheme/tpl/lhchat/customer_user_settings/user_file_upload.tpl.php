<?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/user_file_upload_pre.tpl.php'));?>

<?php if ($user_file_upload_enabled == true) : ?>

<?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>
<?php if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) : ?>
 <li role="menuitem">
<a class="file-uploader" href="#"><i class="material-icons">attach_file</i>
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]" multiple>
</a>
</li>
<?php endif;?>

<?php endif;?>