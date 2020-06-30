<?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus/check_chat_status_multiinclude.tpl.php'));?>
<?php if (!($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && $chat->bot !== null && isset($chat->bot->configuration_array['profile_hide']) && $chat->bot->configuration_array['profile_hide'] == true)) : ?>
<?php if (!isset($customChatStatus) || $customChatStatus == false) : ?>

<?php if (($is_activated == true || $is_proactive_based == true) && ($chat->status != erLhcoreClassModelChat::STATUS_BOT_CHAT)) : ?>
    <?php if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && ($user = $chat->user) !== false) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_main_pre.tpl.php')); ?>
    	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php'));?>
    <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?>
        <h6 class="fs12 status-text"><?php if ($theme !== false  && $theme->bot_status_text != '') : ?>
            <?php echo htmlspecialchars($theme->bot_status_text)?>
        <?php else : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/bot_chat.tpl.php'));?><?php endif; ?></h6>
    <?php elseif ($is_proactive_based == true) : ?>
    <h6 class="fs12 status-text">
    	<?php if ($theme !== false  && $theme->support_joined != '') : ?>
    	   <?php echo htmlspecialchars($theme->support_joined)?>
    	<?php else : ?>
            <?php if ($chat->transfer_uid > 0 && erLhcoreClassModelTransfer::getCount(array('filter' => array('chat_id' => $chat->id))) > 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/transfered_chat.tpl.php'));?>
            <?php else : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/joined_chat.tpl.php'));?>
            <?php endif ?>
    	<?php endif;?>
    </h6>
    <?php endif;?>
    <?php elseif ($is_closed == true) : ?>
    <h6 class="fs12 status-text">
    	<?php if ($theme !== false  && $theme->support_closed != '') : ?>
    	   <?php echo htmlspecialchars($theme->support_closed)?>
    	<?php else : ?>
    	   <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/closed_chat.tpl.php'));?>
    	<?php endif; ?>	
    </h6>
    <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?>

        <?php $user = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);?>
        <?php if ($user instanceof erLhcoreClassModelGenericBotBot) : erLhcoreClassGenericBotWorkflow::setDefaultPhotoNick($chat,$user); $extraMessage = ($theme !== false ? htmlspecialchars($theme->bot_status_text) : ''); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_main_pre.tpl.php')); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php'));?>
        <?php else : ?>
            <h6 class="fs12 status-text"><?php if ($theme !== false  && $theme->bot_status_text != '') : ?>
                <?php echo htmlspecialchars($theme->bot_status_text)?>
            <?php else : ?>
               <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/bot_chat.tpl.php'));?>
            <?php endif; ?></h6>
        <?php endif; ?>

    <?php elseif ($is_online == true) : ?>
    <h6 class="fs12 status-text">
         <?php if ($chat->number_in_queue > 1) : ?>
            <?php if ($theme !== false  && $theme->pending_join_queue != '') : ?>
                 <?php echo htmlspecialchars(str_replace('{number}',$chat->number_in_queue,$theme->pending_join_queue))?>
            <?php else : ?>
                 <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/you_a_number_in_queue.tpl.php'));?>
            <?php endif ?>
         <?php else : ?>
             <?php if ($theme !== false  && $theme->pending_join != '') : ?>
        	   <?php echo htmlspecialchars($theme->pending_join)?>
        	<?php else : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/pending_join.tpl.php'));?>
            <?php endif;?>
        <?php endif;?>
    </h6>
   
    <?php else : ?>
    <h6 class="fs12 status-text">
    <?php if ($theme !== false  && $theme->noonline_operators != '') : ?>
    	   <?php echo htmlspecialchars($theme->noonline_operators)?>
    	<?php else : ?>
    	   <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/no_logged_operators.tpl.php'));?>
        <?php endif;?>
    </h6>
<?php endif; ?>

<?php endif; ?>

<?php endif; ?>

