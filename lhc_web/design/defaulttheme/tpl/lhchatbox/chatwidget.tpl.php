<?php if (isset($chatbox)) : ?>
    <?php if ($chatbox !== false) : ?>
        <div class="row form-group">
            <div class="col-9">
                <span><b><?php echo htmlspecialchars($chatbox->name)?></b></span>
            </div>
        </div>

        <?php if ($chatbox->active == 1) : ?>

            <div id="messages" class="form-group">
                <div id="messagesBlockWrap">
                    <div class="msgBlock" id="messagesBlock" style="height:<?php isset($chatbox_chat_height) ? print (int)$chatbox_chat_height : print 220?>px"><?php
                        $lastMessageID = 0;
                        $messages = erLhcoreClassChat::getChatMessages($chatbox->chat->id);
                        $chat = $chatbox->chat; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchatbox/syncuser.tpl.php'));?>
                        <?php if (isset($msg)) { $lastMessageID = $msg['id'];} ?>
                    </div>
                </div>
            </div>

            <div id="ChatMessageContainer" class="p-2">

                <div>
                    <?php if (isset($_GET['dnc']) && $_GET['dnc'] == 'true') : ?>
                        <input type="hidden" class="mt5 mb-0" value="<?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES); ?>" id="CSChatNick" />
                    <?php else : ?>
                        <input class="form-control form-control-sm mb-2" type="text" placholder="Nick" title="Nick" value="<?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES); ?>" id="CSChatNick" />
                    <?php endif;?>
                </div>

                <?php if (isset($_GET['dnc']) && $_GET['dnc'] == 'true') : ?>
                    <span><?php echo htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES); ?></span>
                <?php endif;?>

                <div class="position-relative">
                <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings.tpl.php'));?>
                </div>

                <script type="text/javascript">
                    jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
                	   lhinst.addmsguserchatbox();
                	   return false;
                    });
                    lhinst.afterChatWidgetInit();
                </script>
            </div>

            <script type="text/javascript">
                lhinst.setChatID('<?php echo $chatbox->chat->id?>');
                lhinst.setChatHash('<?php echo $chatbox->chat->hash?>');
                lhinst.setLastUserMessageID('<?php echo $lastMessageID;?>');
                lhinst.setWidgetMode(true);
                lhinst.setSyncUserURL('chatbox/syncuser/');

                $( window ).on('load',function() {
                    setTimeout(function(){
                        $('#messagesBlock').scrollTop($('#messagesBlock').prop('scrollHeight'));
                    },100);
                });

                lhinst.scheduleSync();
            </script>
        <?php else : ?>
            <div class="alert alert-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chatbox is disabled.')?></div>
        <?php endif;?>
    <?php else : ?>
        <div class="alert alert-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Invalid hash or auto creation is disabled')?></div>
    <?php endif;?>
<?php else : ?>
    <?php if (isset($errors)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>
<?php endif;?>