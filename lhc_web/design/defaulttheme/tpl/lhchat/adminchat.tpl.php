<div class="row">
	<div class="col-xl-8 ps-0 chat-main-left-column" id="chat-main-column-<?php echo $chat->id;?>">

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

            <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
                <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat was closed. You can not write messages anymore.')?>
            <?php elseif ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT && $chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?>

                <?php if (isset($writeRemoteDisabled) && $writeRemoteDisabled === true) : ?>
                    <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You can only read a messages.')?>
                <?php else : ?>
                    <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are not chat owner, type with caution.')?>
                <?php endif;?>

            <?php else : ?>
                <?php $placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Switch between chats using Alt+') . '&#8593;&#8595 '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','arrows') . '. ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Search for canned messages by using their tags #hash. You can drop files here.')?>
            <?php endif;?>

            <div class="d-flex flex-wrap toolbar-chat" translate="no">
                <div class="me-auto">
                <div class="btn-group btn-group-sm me-2 pb-1" role="group">
                    <?php $whisperMode = ($chat->user_id > 0 && $chat->user_id != erLhcoreClassUser::instance()->getUserID()) && $chat->status != erLhcoreClassModelChat::STATUS_BOT_CHAT; ?>
                    <button type="button" data-plc="<?php echo $placeholderValue?>" class="btn btn-sm<?php ($whisperMode) ? print ' btn-outline-secondary' : print ' btn-outline-primary';?>" id="chat-write-button-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Write')?>"><i class="material-icons me-0">create</i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="chat-preview-button-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Preview')?>"><i class="material-icons me-0">visibility</i></button>
                    <button type="button" class="btn btn-sm<?php ($whisperMode) ? print ' btn-outline-primary' : print ' btn-outline-secondary';?>" id="chat-whisper-button-<?php echo $chat->id?>" data-plc="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are in whisper mode!')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Whisper')?>"><i class="material-icons me-0">hearing</i></button>
                    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','impersonate') && $chat->user_id > 0 && $chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="chat-impersonate-option-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Impersonate')?>"><i class="material-icons me-0">supervisor_account</i></button>
                    <?php endif; ?>
                </div>
                </div>

                <div class="ms-auto">
                    <?php $bbcodeOptions = array('selector' => '#CSChatMessage-' . $chat->id) ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/toolbar_text_area.tpl.php')); ?>
                </div>
            </div>

            <?php if ($whisperMode) {$placeholderValue = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are in whisper mode!'); } ?>

            <?php if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT && !(isset($writeRemoteDisabled) && $writeRemoteDisabled === true) && erLhcoreClassUser::instance()->hasAccessTo('lhchat','impersonate') && $chat->user_id > 0 && $chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?>
            <div id="chat-join-as-container-<?php echo $chat->id?>" class="btn-group btn-group-sm mode-write-chat position-absolute<?php $whisperMode == true ? print ' hide' : ''?>" role="group">
                <select class="form-control form-control-sm rounded-0" id="chat-mode-selected-<?php echo $chat->id?>">
                    <option data-plc="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are not chat owner, type with caution.')?>" value="me"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Me')?></option>
                    <option data-plc="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are working as a chat owner.')?>" value="op"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chat owner')?></option>
                </select>
                <button class="btn btn-sm btn-secondary rounded-0" type="button" id="chat-join-as-<?php echo $chat->id?>" style="white-space: nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Join as')?></button>
            </div>
            <?php endif; ?>

		    <textarea <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviouvis')) : ?>edit-vis="true"<?php endif;?> <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviousop')) : ?>edit-op="true"<?php endif;?> <?php if (!erLhcoreClassUser::instance()->hasAccessTo('lhchat','editprevious')) : ?>disable-edit="true"<?php endif;?> <?php if ($whisperMode) : ?>whisper="1"<?php endif;?> <?php !erLhcoreClassChat::hasAccessToWrite($chat) || ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT && $chat->user_id != 0 && $chat->user_id != erLhcoreClassUser::instance()->getUserID() && !erLhcoreClassUser::instance()->hasAccessTo('lhchat','writeremotechat') && $writeRemoteDisabled = true) ? print 'readonly="readonly"'  : '' ?> title="<?php echo $placeholderValue?>" placeholder="<?php echo $placeholderValue?>" class="form-control form-control-sm form-send-textarea form-group<?php if ($chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?> form-control-warning<?php endif;?>" data-rows-default="<?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_text_rows',2)?>" rows="<?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_text_rows',2)?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>readonly="readonly"<?php endif;?> name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"></textarea>
            <div id="chat-preview-container-<?php echo $chat->id?>" style="display: none; min-height: 59px"></div>
        </div>
        
		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_text_area_block.tpl.php')); ?>

	</div>
	<div class="col-xl-4 chat-main-right-column mh150" translate="no" id="chat-right-column-<?php echo $chat->id;?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>
</div>

<script type="text/javascript">lhinst.addAdminChatFinished(<?php echo $chat->id;?>,<?php echo $LastMessageID?>,<?php isset($arg) ? print json_encode($arg) : print 'null'?>);</script>
