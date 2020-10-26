<div class="row">
	<div class="col-sm-7 chat-main-left-column" id="chat-main-column-<?php echo $chat->id;?>">

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

			<div class="msgBlock msgBlock-admin" id="messagesBlock-<?php echo $chat->id?>">
				<?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
				<?php if (isset($msg)) {	$LastMessageID = $msg['id'];} ?>

				<?php if ($chat->user_status == 1) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userleftchat.tpl.php')); ?>
				<?php elseif ($chat->user_status == 0) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhchat/userjoined.tpl.php')); ?>
				<?php endif;?>
			</div>
			
		</div>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_textarea.tpl.php')); ?>
		
		<div class="user-is-typing" id="user-is-typing-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','User is typing now...')?></div>
		
		<div class="message-container-admin">

            <div class="d-flex flex-nowrap">
                <div class="flex-shrink-1 ">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="chat-write-button-<?php echo $chat->id?>"><i class="material-icons mr-0">create</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Write')?></button>&nbsp;<button type="button" class="btn btn-sm btn-outline-secondary" id="chat-preview-button-<?php echo $chat->id?>"><i class="material-icons mr-0">visibility</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Preview')?></button>
                </div>
                <div class="ml-auto">
                    <?php $bbcodeOptions = array('selector' => '#CSChatMessage-' . $chat->id) ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/toolbar_text_area.tpl.php')); ?>
                </div>
            </div>

		    <textarea <?php !erLhcoreClassChat::hasAccessToWrite($chat) ? print 'readonly="readonly"' : '' ?> placeholder="<?php if ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT && $chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are not chat owner, type with caution')?><?php endif;?>" class="form-control form-control-sm form-send-textarea form-group<?php if ($chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?> form-control-warning<?php endif;?>" rows="2" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>readonly="readonly"<?php endif;?> name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"></textarea>
            <div id="chat-preview-container-<?php echo $chat->id?>" style="display: none; min-height: 59px"></div>
        </div>
        
		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_text_area_block.tpl.php')); ?>

	</div>
	<div class="col-sm-5 chat-main-right-column" id="chat-right-column-<?php echo $chat->id;?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>
</div>

<script type="text/javascript">lhinst.addAdminChatFinished(<?php echo $chat->id;?>,<?php echo $LastMessageID?>,<?php isset($arg) ? print json_encode($arg) : print 'null'?>);</script>
