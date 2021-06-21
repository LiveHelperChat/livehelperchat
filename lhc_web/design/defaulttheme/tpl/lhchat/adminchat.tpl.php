<div class="row">
	<div class="col-sm-8 pl-0 chat-main-left-column" id="chat-main-column-<?php echo $chat->id;?>">

        <span class="last-user-msg" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Last visitor message time')?>"><i class="material-icons">access_time</i><span id="last-msg-chat-<?php echo $chat->id?>">...</span></span>

		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Show/Hide right column')?>" href="#" class="material-icons collapse-right" onclick="lhinst.processCollapse('<?php echo $chat->id;?>')">chevron_right</a>
		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_messages_block.tpl.php')); ?>

		<div class="message-block">
            <?php
                $LastMessageID = 0;
                $messages = erLhcoreClassChat::getChatMessages($chat->id, erLhcoreClassChat::$limitMessages);
                $current_user_id = erLhcoreClassUser::instance()->getUserID();
            ?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/load_previous.tpl.php'));?>

			<div class="msgBlock msgBlock-admin" id="messagesBlock-<?php echo $chat->id?>" onscroll="lhinst.onScrollAdmin(<?php echo $chat->id?>)">
				<?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
				<?php if (isset($msg)) {	$LastMessageID = $msg['id'];} ?>

				<?php if ($chat->user_status == 1) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userleftchat.tpl.php')); ?>
				<?php elseif ($chat->user_status == 0) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userjoined.tpl.php')); ?>
				<?php endif;?>
			</div>

            <div class="position-absolute btn-bottom-scroll fade-in-fast d-none" id="scroll-button-admin-<?php echo $chat->id?>">
                <button type="button" onclick="lhinst.scrollToTheBottomMessage(<?php echo $chat->id?>)" class="btn btn-sm btn-secondary" data-new="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New message!')?>" data-default="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','↓ Scroll to the bottom')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','↓ Scroll to the bottom')?></button>
            </div>

		</div>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_textarea.tpl.php')); ?>
		
		<div class="user-is-typing" id="user-is-typing-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','User is typing now...')?></div>
		
		<div class="message-container-admin" >

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files_tab_pre.tpl.php'));?>
            <?php if ($information_tab_user_files_tab_enabled == true) : ?>
            <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>
            <?php if ( isset($fileData['active_admin_upload']) && $fileData['active_admin_upload'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator') ) : $filesEnabled = true;?>
                <input id="fileupload-<?php echo $chat->id?>" class="fs12 d-none" type="file" name="files[]" multiple>
                <script>
                    lhinst.addFileUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',chat_id:'<?php echo $chat->id?>',fs:<?php echo $fileData['fs_max']*1024?>,ft_op:/(\.|\/)(<?php echo $fileData['ft_op']?>)$/i});
                </script>
            <?php endif; endif;?>

            <div class="d-flex flex-nowrap" translate="no">
                <div class="flex-shrink-1 ">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="chat-write-button-<?php echo $chat->id?>"><i class="material-icons mr-0">create</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Write')?></button>&nbsp;<button type="button" class="btn btn-sm btn-outline-secondary" id="chat-preview-button-<?php echo $chat->id?>"><i class="material-icons mr-0">visibility</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Preview')?></button>
                </div>
                <div class="ml-auto">
                    <?php $bbcodeOptions = array('selector' => '#CSChatMessage-' . $chat->id) ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/toolbar_text_area.tpl.php')); ?>
                </div>
            </div>

            <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
                <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat was closed. You can not write messages anymore.')?>
            <?php elseif ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT && $chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?><?php if (isset($writeRemoteDisabled) && $writeRemoteDisabled === true) : ?>
                <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You can only read a messages.')?>
            <?php else : ?>
                <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are not chat owner, type with caution.')?><?php endif;?><?php else :?><?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Switch between chats using Alt+') . '&#8593;&#8595 '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','arrows') . '. ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Search for canned messages by using their tags #hash. You can drop files here.')?>
            <?php endif;?>

		    <textarea <?php !erLhcoreClassChat::hasAccessToWrite($chat) || ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT && $chat->user_id != 0 && $chat->user_id != erLhcoreClassUser::instance()->getUserID() && !erLhcoreClassUser::instance()->hasAccessTo('lhchat','writeremotechat') && $writeRemoteDisabled = true) ? print 'readonly="readonly"'  : '' ?> title="<?php echo $placeholderValue?>" placeholder="<?php echo $placeholderValue?>" class="form-control form-control-sm form-send-textarea form-group<?php if ($chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?> form-control-warning<?php endif;?>" rows="2" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>readonly="readonly"<?php endif;?> name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"></textarea>
            <div id="chat-preview-container-<?php echo $chat->id?>" style="display: none; min-height: 59px"></div>
        </div>
        
		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_text_area_block.tpl.php')); ?>

	</div>
	<div class="col-sm-4 chat-main-right-column" translate="no" id="chat-right-column-<?php echo $chat->id;?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>
</div>

<script type="text/javascript">lhinst.addAdminChatFinished(<?php echo $chat->id;?>,<?php echo $LastMessageID?>,<?php isset($arg) ? print json_encode($arg) : print 'null'?>);</script>
