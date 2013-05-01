<?php if ($chatbox !== false) : ?>
<div class="row">
	<div class="columns small-10">
		<h4><?php echo htmlspecialchars($chatbox->name)?></h4>
	</div>
	<div class="columns small-2">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings.tpl.php'));?>
	</div>
</div>

<?php if ($chatbox->active == 1) : ?>

    <div id="messages" >
        <div class="msgBlock" id="messagesBlock" style="height:<?php isset($chatbox_chat_height) ? print (int)$chatbox_chat_height : print 220?>px"><?php
        $lastMessageID = 0;
        $messages = erLhcoreClassChat::getChatMessages($chatbox->chat->id);
        $chat = $chatbox->chat; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchatbox/syncuser.tpl.php'));?>
        <?php if (isset($msg)) { $lastMessageID = $msg['id'];} ?>
       </div>
    </div>

    <div>
    <input type="text" class="mb5 mt5" placholder="Nick" title="Nick" value="<?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName()); ?>" id="CSChatNick" />
	</div>

    <div>
        <textarea rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage" ></textarea>
        <script type="text/javascript">
        jQuery('#CSChatMessage').bind('keyup', 'return', function (evt){
            lhinst.addmsguserchatbox();
        });
        </script>
    </div>

    <input type="button" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguserchatbox()" />
    <br>

<script type="text/javascript">
    lhinst.setChatID('<?php echo $chatbox->chat->id?>');
    lhinst.setChatHash('<?php echo $chatbox->chat->hash?>');
    lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');
    lhinst.setWidgetMode(true);
    lhinst.setSyncUserURL('chatbox/syncuser/');
	$('#messagesBlock').animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 1000);
    lhinst.syncusercall();
</script>
<?php else : ?>
<div class="alert-box alert round"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chatbox is disabled.')?></div>
<?php endif;?>
<?php else : ?>
<div class="alert-box alert round"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Invalid hash or auto creation is disabled')?></div>
<?php endif;?>