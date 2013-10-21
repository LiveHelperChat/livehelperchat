<div>
	<ul class="circle fs11">
		<?php foreach (erLhcoreClassChat::getList(array('filter' => array('chat_id' => $chat->id)),'erLhcoreClassModelChatFile','lh_chat_file') as $file) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" class="link" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Download file')?> - <?php echo htmlspecialchars($file->upload_name).' ['.$file->extension.']'?> </a></li>
		<?php endforeach;?>
	</ul>
</div>


<!-- The file input field used as target for the file upload widget -->
<input id="fileupload-<?php echo $chat->id?>" class="fs11" type="file" name="files[]" multiple>



<div class="drop-zone" id="drop-zone-<?php echo $chat->id?>">
	<div class="drop-title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Drop your files here.')?></div>
</div>

<script>
lhinst.addFileUpload(<?php echo $chat->id?>);
</script>