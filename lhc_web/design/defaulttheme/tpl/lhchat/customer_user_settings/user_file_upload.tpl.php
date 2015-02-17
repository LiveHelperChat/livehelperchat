<?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>

 <?php if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) : ?>
 <li>
 <a class="file-uploader icon-attach" href="#">
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]" multiple>
 </a>
 </li>
 <?php endif;?>