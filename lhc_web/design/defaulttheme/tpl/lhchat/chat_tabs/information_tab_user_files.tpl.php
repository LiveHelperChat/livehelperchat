<div>
    <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Refresh')?>" class="btn btn-default" onclick="lhinst.updateChatFiles('<?php echo $chat->id?>')" />

	<ul id="chat-files-list-<?php echo $chat->id?>">
		<?php foreach (erLhcoreClassChat::getList(array('filter' => array('chat_id' => $chat->id)),'erLhcoreClassModelChatFile','lh_chat_file') as $file) : ?>
			<li id="file-id-<?php echo $file->id?>"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Delete file')?>" onclick="return lhinst.deleteChatfile('<?php echo $file->id?>')" class="material-icons">delete</a> <a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" class="link" target="_blank"><?php if ($file->user_id == 0) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Sent by Customer')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Sent by Operator')?><?php endif;?> - <?php echo htmlspecialchars($file->upload_name).' ['.$file->extension.']'?> </a></li>
		<?php endforeach;?>
	</ul>
</div>

<div class="form-group">
<input id="fileupload-<?php echo $chat->id?>" class="fs12" type="file" name="files[]" multiple>
</div>

<div class="drop-zone form-group" id="drop-zone-<?php echo $chat->id?>">
	<div class="drop-title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Drop your files here.')?></div>
</div>

<script>
lhinst.addFileUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',chat_id:'<?php echo $chat->id?>',fs:<?php echo $fileData['fs_max']*1024?>,ft_op:/(\.|\/)(<?php echo $fileData['ft_op']?>)$/i});
</script>