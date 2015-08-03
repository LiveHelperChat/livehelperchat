<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/attatch_file_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_attatch_file_enabled == true) : ?>
<a class="material-icons mr-0" onclick="return lhc.revealModal({'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'file/attatchfile/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Attach uploaded file')?>">attach_file</a>
<?php endif;?>