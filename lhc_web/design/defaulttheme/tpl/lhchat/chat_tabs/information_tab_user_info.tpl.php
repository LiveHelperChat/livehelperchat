<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information/information_top.tpl.php'));?>

<div class="pull-right operator-info pt5">
	<i class="material-icons mr-0<?php if ($chat->fbst == 1) : ?> up-voted<?php endif;?>">thumb_up</i>
	<i class="material-icons<?php if ($chat->fbst == 2) : ?> down-voted<?php endif;?>">thumb_down</i>
	
	<i id="chat-id-<?php echo $chat->id?>-mds" class="material-icons<?php if ($chat->has_unread_op_messages == 1) : ?> chat-unread<?php else : ?> chat-active<?php endif;?>">chat</i>

    <?php if (!isset($hideActionBlock) || $hideActionBlock == false) : ?>
	<span class="pull-right <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?> action-image<?php endif?>" id="chat-status-text-<?php echo $chat->id?>" <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Click to change chat status')?>" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/changestatus/<?php echo $chat->id?>'})"<?php endif;?>>
		<i class="material-icons mr-0" title="<?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Pending chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Active chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closed chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chatbox chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Operators chat')?><?php endif;?>">info_outline</i>
	</span>
	<?php endif; ?>

</div>
	 
<div>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/actions_order.tpl.php'));?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/actions_order_extension_multiinclude.tpl.php'));?>
    
    <?php foreach ($orderChatButtons as $buttonData) : ?>
        <?php if ($buttonData['enabled'] == true) : ?>
            <?php if ($buttonData['item'] == 'edit_chat') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/edit_chat.tpl.php'));?>
           	<?php elseif ($buttonData['item'] == 'open_new_window') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/open_new_window.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'show_survey') : ?>		
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/show_survey.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'remove_dialog_tab') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/remove_dialog_tab.tpl.php'));?>	
        	<?php elseif ($buttonData['item'] == 'close_chat') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/close_chat.tpl.php'));?>	
        	<?php elseif ($buttonData['item'] == 'delete_chat') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/delete_chat.tpl.php'));?>	
        	<?php elseif ($buttonData['item'] == 'transfer') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'blockuser') : ?>
            	<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowblockusers')) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser.tpl.php'));?>
            	<?php endif;?>
        	<?php elseif ($buttonData['item'] == 'send_mail') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/send_mail.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'redirect_contact') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_contact.tpl.php'));?>
            <?php elseif ($buttonData['item'] == 'print') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/print.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'attatch_file') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/attatch_file.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'redirect_user') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_user.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'speech') : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/speech.tpl.php'));?>
        	<?php elseif ($buttonData['item'] == 'cobrowse') : ?>	
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/cobrowse.tpl.php'));?>
            <?php elseif ($buttonData['item'] == 'print_archive') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/print_archive.tpl.php'));?>
            <?php elseif ($buttonData['item'] == 'mail_archive') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/mail_archive.tpl.php'));?>
            <?php elseif ($buttonData['item'] == 'copy_messages') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/copy_messages.tpl.php'));?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_actions_extension_multiinclude.tpl.php'));?>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/information_order.tpl.php'));?>

<table class="table table-condensed">
<?php foreach ($orderInformation as $buttonData) : ?>
    <?php if ($buttonData['enabled'] == true) : ?>
        <?php if ($buttonData['item'] == 'department') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/above_department_extension_multiinclude.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/department.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'product') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/product.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'uagent') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/uagent.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'country_code') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/country_code.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'user_tz_identifier') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/user_tz_identifier.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'city') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/city.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'ip') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/ip.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'referrer') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/referrer.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'session_referrer') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/session_referrer.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'id') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/id.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'email') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/email.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'phone') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/phone.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/after_phone_extension_multiinclude.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'additional_data') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/additional_data.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'created') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/created.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'user_left') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/user_left.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'wait_time') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/wait_time.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'chat_duration') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/chat_duration.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'chat_status') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/chat_status.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'chat_owner') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/chat_owner.tpl.php'));?>
        <?php else : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/extension_information_row_multiinclude.tpl.php'));?>
        <?php endif;?>
    <?php endif; ?>
<?php endforeach; ?>
</table>
