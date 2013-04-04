<div class="row">

	<div class="columns eleven mobile-three">
		<h2 id="status-chat"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h2>
	</div>

	<div class="columns one mobile-one">

		<?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings.tpl.php'));?>
	</div>

</div>


    <div id="messages" >
        <div class="msgBlock" id="messagesBlock"><?php
        $lastMessageID = 0;

        foreach (erLhcoreClassChat::getChatMessages($chat_id) as $msg) : ?>
            <?php if ($msg['user_id'] == 0) { ?>
            	<div class="message-row"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date('H:i:s',$msg['time']);} else { echo date('Y-m-d H:i:s',$msg['time']);}; ?></div><span class="usr-tit"><?php if (isset($chat_widget_mode) && $chat_widget_mode == true) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_green.png');?>" title="<?php echo htmlspecialchars($chat->nick)?>" alt="<?php echo htmlspecialchars($chat->nick)?>" /><?php else : ?><?php echo htmlspecialchars($chat->nick)?>:<?php endif;?>&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } else { ?>
                <div class="message-row response"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date('H:i:s',$msg['time']);} else {	echo date('Y-m-d H:i:s',$msg['time']);}; ?></div><span class="usr-tit"><?php if (isset($chat_widget_mode) && $chat_widget_mode == true) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit.png');?>" title="<?php echo htmlspecialchars($msg['name_support'])?>" alt="<?php echo htmlspecialchars($msg['name_support'])?>" /><?php else : ?><?php echo htmlspecialchars($msg['name_support'])?>:<?php endif;?>&nbsp;</span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></div>
            <?php } ?>
         <?php $lastMessageID = $msg['id']; endforeach; ?>
       </div>
       <div id="id-operator-typing">
            <i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...')?></i>
       </div>
    </div>
    <br>
    <div>
        <textarea rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage" ></textarea>
        <script type="text/javascript">
        jQuery('#CSChatMessage').bind('keyup', 'return', function (evt){
            lhinst.addmsguser();
        });

        lhinst.initTypingMonitoringUser('<?php echo $chat_id?>');
        </script>
    </div>



    <input type="button" class="small button round" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguser()" />

    <br>
    <br>

<script type="text/javascript">
    lhinst.setChatID('<?php echo $chat_id?>');
    lhinst.setChatHash('<?php echo $hash?>');
    lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');

    <?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>
    lhinst.setWidgetMode(true);
	<?php endif; ?>

	$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 1000);

    // Start user chat synchronization
    lhinst.chatsyncuserpending();
    lhinst.syncusercall();

    $(window).bind('beforeunload', function(){
        lhinst.userclosedchat();
    });

</script>