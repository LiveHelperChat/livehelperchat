<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_status.tpl.php')); ?>

<?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
    <script>lhinst.setWidgetMode(true);</script>
<?php endif; ?>

<?php if (
    $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT ||
    $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT ||
    $chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT ||
    ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->last_op_msg_time > time() - (int)erLhcoreClassModelChatConfig::fetch('open_closed_chat_timeout')->current_value) ||
    (isset($paid_chat_params['allow_read']) && $paid_chat_params['allow_read'] == true)) : ?>
    <div id="messages" class="<?php if (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['hide_visitor_profile']) && $theme->bot_configuration_array['hide_visitor_profile'] == 1) : ?>hide-visitor-profile <?php endif;?> <?php if (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1) : ?>bubble-messages <?php endif;?> <?php if (isset($fullheight) && $fullheight == true) : ?>fullheight<?php endif ?>">
        <div id="messagesBlockWrap">
            <div class="msgBlock<?php if (isset($theme) && $theme !== false && $theme->hide_ts == 1) : ?> msg-hide-ts<?php endif?>" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="<?php if (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['msg_expand']) && $theme->bot_configuration_array['msg_expand'] == 1) : ?>max-<?php endif;?>height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php else : ?>style="height:300px;"<?php endif?> id="messagesBlock"><?php
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

    <div id="ChatMessageContainer" class="<?php if (isset($chat_started_now) && $chat_started_now === true && $chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && $chat->bot !== null && isset($chat->bot->configuration_array['msg_hide']) && $chat->bot->configuration_array['msg_hide'] == true) : ?>hide<?php endif;?>">

        <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings.tpl.php'));?>

        <script type="text/javascript">
        jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
        	 lhinst.addmsguser();
        	 return false;
        }); 

        <?php if (!(isset($theme) && $theme !== false && isset($theme->bot_configuration_array['disable_edit_prev']) && $theme->bot_configuration_array['disable_edit_prev'] == true)) : ?>
        jQuery('#CSChatMessage').bind('keyup', 'up', function (evt){
            lhinst.editPreviousUser();
        });
        <?php endif; ?>

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

	$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));

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