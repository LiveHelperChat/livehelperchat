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
	    <?php if (isset($_GET['dnc']) && $_GET['dnc'] == 'true') : ?>
	    <input type="hidden" class="mt5 mb0" value="<?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES); ?>" id="CSChatNick" />
	    <?php else : ?>
	    <input type="text" class="mt5 mb0" placholder="Nick" title="Nick" value="<?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES); ?>" id="CSChatNick" />
	    <?php endif;?>
	</div>

    <div class="pt5">
    	<?php if (isset($_GET['dnc']) && $_GET['dnc'] == 'true') : ?>
    		<h5><?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES); ?></h5>
    	<?php endif;?>
        <textarea rows="4" name="ChatMessage" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Enter your message')?>" id="CSChatMessage"></textarea>
        <script type="text/javascript">
        jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
            lhinst.addmsguserchatbox();
            return false;
        });
        </script>
    </div>

   <div id="bbcodeReveal" class="reveal-modal"></div>

	<div class="pt5">
    	<input type="button" class="tiny round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguserchatbox()" />
    	
    	<input type="button" class="tiny round button secondary right" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','BB Code')?>" data-reveal-id="bbcodeReveal" data-reveal-ajax="<?php echo erLhcoreClassDesign::baseurl('chat/bbcodeinsert')?>" />

    </div>

<script type="text/javascript">
    lhinst.setChatID('<?php echo $chatbox->chat->id?>');
    lhinst.setChatHash('<?php echo $chatbox->chat->hash?>');
    lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');
    lhinst.setWidgetMode(true);
    lhinst.setSyncUserURL('chatbox/syncuser/');
    
    $( window ).load(function() {
    	setTimeout(function(){
    		$('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
    	},100);
    });
	
    lhinst.scheduleSync();
</script>
<?php else : ?>
<div class="alert-box alert round"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chatbox is disabled.')?></div>
<?php endif;?>
<?php else : ?>
<div class="alert-box alert round"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Invalid hash or auto creation is disabled')?></div>
<?php endif;?>