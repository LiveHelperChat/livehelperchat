<div class="row">

	<div class="col-9">
		<div id="status-chat">
		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
			<h6 class="fs12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat is closed.'); ?></h6>
	   <?php elseif (($user = $chat->user) !== false) : ?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_main_pre.tpl.php')); ?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php')); ?>
		<?php else : ?>
            <h6 class="fs12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h6>
		<?php endif; ?>
		</div>
				
		<?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value == 1 && erLhcoreClassChat::canReopen($chat) ) : ?>
			<a href="<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $chat->id?>/<?php echo $chat->hash?><?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>/(mode)/widget<?php endif; ?><?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>/(embedmode)/embed<?php endif;?>" class="btn btn-secondary" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?></a>
		<?php endif; ?>
						
		<?php /*if (!isset($paid_chat_params['allow_read']) || $paid_chat_params['allow_read'] == false) : ?>
    		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && ( (isset($chat_widget_mode) && $chat_widget_mode == true && $chat->time < time()-1800)) ) : ?>
    			<input type="button" class="btn btn-secondary mb-1" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
    		<?php endif;?>
		<?php endif;*/?>
		
	</div>
</div>

<?php if (
    $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT ||
    $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT ||
    $chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT ||
    ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->last_op_msg_time > time() - (int)erLhcoreClassModelChatConfig::fetch('open_closed_chat_timeout')->current_value) ||
    (isset($paid_chat_params['allow_read']) && $paid_chat_params['allow_read'] == true)) : ?>
    <div id="messages"<?php if (isset($fullheight) && $fullheight == true) : ?> class="fullheight"<?php endif ?>>
        <div id="messagesBlockWrap">
            <div class="msgBlock<?php if (isset($theme) && $theme !== false && $theme->hide_ts == 1) : ?> msg-hide-ts<?php endif?>" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php endif?> id="messagesBlock"><?php
            $lastMessageID = 0;
            $lastOperatorChanged = false;
            $lastOperatorId = false;
            $lastOperatorNick = '';

            $messages = erLhcoreClassChat::getChatMessages($chat_id);
            $messagesStats = array(
                'total_messages' => count($messages),
                'counter_messages' => 0,
            );

            foreach ($messages as $msg) :

            $messagesStats['counter_messages']++;

            if ($lastOperatorId !== false && ($lastOperatorId != $msg['user_id'] || $msg['name_support'] != $lastOperatorNick)) {
                $lastOperatorChanged = true;
            } else {
                $lastOperatorChanged = false;
            }

            $lastOperatorId = $msg['user_id'];
            $lastOperatorNick = $msg['name_support'];
            ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
            <?php $lastMessageID = $msg['id'];
             endforeach; ?>
           </div>
            <div id="chat-progress-status" class="hide"></div>
        </div>
    </div>
    <div id="id-operator-typing"></div>
 
    <?php if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
    <div id="ChatMessageContainer">

        <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings.tpl.php'));?>

        <script type="text/javascript">
        jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
        	 lhinst.addmsguser();
        	 return false;
        }); 

        jQuery('#CSChatMessage').bind('keyup', 'up', function (evt){
        	 lhinst.editPreviousUser();
		});

        lhinst.initTypingMonitoringUser('<?php echo $chat_id?>');
        lhinst.afterUserChatInit();
        </script>
        
    </div>
     <?php endif;?>
<script type="text/javascript">
    lhinst.setChatID('<?php echo $chat_id?>');
    lhinst.setChatHash('<?php echo $hash?>');
    lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');

    <?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
        lhinst.setWidgetMode(true);
        <?php if (isset($fullheight) && $fullheight == true) : ?>
            var fullHeightFunction = function() {
                var bodyHeight = $(document.body).outerHeight();
                var messageBlockHeight = $('#messages').outerHeight();
                var widgetLayoutHeight = $('#widget-layout').outerHeight();

                var messageBlockFullHeight = bodyHeight - (widgetLayoutHeight - messageBlockHeight) - 10;

                $('#messagesBlockWrap').height(messageBlockFullHeight);
                $('#messagesBlock').css('max-height',messageBlockFullHeight);
                setTimeout(fullHeightFunction, 200);
            };
            setTimeout(fullHeightFunction, 200);
        <?php endif; ?>
	<?php endif; ?>

    <?php if ( isset($theme) && $theme !== false ) : ?>
    lhinst.setTheme('<?php echo $theme->id?>');
	<?php endif; ?>

    <?php if ( isset($survey) && $survey !== false ) : ?>
    lhinst.setSurvey('<?php echo $survey?>');
	<?php endif; ?>

	<?php if (isset($chat_embed_mode) && $chat_embed_mode == true) : ?>
	lhinst.setEmbedMode(true);
    <?php endif;?>
	
	setTimeout(function(){
			$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
	},100);
	
    // Start user chat synchronization
    lhinst.chatsyncuserpending();    
    lhinst.scheduleSync();

    $( document ).ready(function() {
        if (jQuery('#CSChatMessage').length > 0) {
        	jQuery('#CSChatMessage').focus();    
        	jQuery('#CSChatMessage')[0].setSelectionRange(1000,1000);
    	}
    });
    
    $(window).bind('beforeunload', function(){
        lhinst.userclosedchat();
    });
</script>
<?php endif;?>