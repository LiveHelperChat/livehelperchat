<div class="p-0 mb-0 border-bottom clearfix">
    <button class="navbar-toggler navbar-light float-right" ng-click="lhc.toggleList('cmtoggle')" type="button" aria-expanded="true" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon bg-light border rounded"></span>
    </button>
    <div class="pt-1"><small><span class="text-secondary" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Last visitor message time')?>"><i class="material-icons">access_time</i><span id="last-msg-chat-<?php echo $chat->id?>">...</span></span></small></div>
</div>

<div class="row chat-messages-body" ng-class="{ctoggled: cmtoggle}">
	<div class="col-md-7 chat-main-left-column" id="chat-main-column-<?php echo $chat->id;?>">

		<?php include(erLhcoreClassDesign::designtpl('lhchat/part/above_messages_block.tpl.php')); ?>

		<div class="message-block">
            <?php $LastMessageID = 0; $messages = erLhcoreClassChat::getChatMessages($chat->id, erLhcoreClassChat::$limitMessages); ?>

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
		
		<div class="message-container-admin row">
            <div class="col-10 col-md-12">
		        <textarea <?php !erLhcoreClassChat::hasAccessToWrite($chat) ? print 'readonly="readonly"' : '' ?> placeholder="<?php if ($chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You are not chat owner, type with caution')?><?php endif;?>" class="form-control form-control-sm form-group<?php if ($chat->user_id != erLhcoreClassUser::instance()->getUserID()) : ?> form-control-warning<?php endif;?>" rows="2" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>readonly="readonly"<?php endif;?> name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>"></textarea>
            </div>
            <div class="col-2 d-block d-md-none pl-0">
                <nav class="navbar navbar-light bg-white p-0">
                    <button class="navbar-toggler mx-auto" onclick="$(this).attr('aria-expanded') == 'false' ? $('#chat-main-column-<?php echo $chat->id?>').addClass('bottom-expanded') : $('#chat-main-column-<?php echo $chat->id?>').removeClass('bottom-expanded')" type="button" data-toggle="collapse" data-target="#chat-admin-control-<?php echo $chat->id?>" aria-controls="chat-admin-control-<?php echo $chat->id?>" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </nav>
            </div>
        </div>

        <div class="navbar-expand-md w-100">
            <div class="collapse navbar-collapse w-100" id="chat-admin-control-<?php echo $chat->id?>">
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_text_area_block.tpl.php')); ?>
            </div>
        </div>

	</div>

	<div class="col-md-5 chat-main-right-column order-first order-md-2 pt-1" id="chat-right-column-<?php echo $chat->id;?>">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_tabs_container.tpl.php')); ?>
	</div>

</div>

<script type="text/javascript">lhinst.addAdminChatFinished(<?php echo $chat->id;?>,<?php echo $LastMessageID?>,<?php isset($arg) ? print json_encode($arg) : print 'null'?>);</script>
