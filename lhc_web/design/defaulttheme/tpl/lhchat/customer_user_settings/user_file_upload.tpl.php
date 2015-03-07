<?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/user_file_upload_pre.tpl.php'));?>

<?php if ($user_file_upload_enabled == true) : ?>

<?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>
<?php if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) : ?>
 <li>
<a class="file-uploader icon-attach" href="#">
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]" multiple>
</a>
</li>
<?php endif;?>

<?php endif;?>