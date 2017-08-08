<?php if ($is_activated == true || $is_proactive_based == true) : ?>
    <?php if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && ($user = $chat->user) !== false) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_main_pre.tpl.php')); ?>
    	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php'));?>
    <?php elseif ($is_proactive_based == true) : ?>
    <h4>
    	<?php if ($theme !== false  && $theme->support_joined != '') : ?>
    	   <?php echo htmlspecialchars($theme->support_joined)?>
    	<?php else : ?>
            <?php if ($chat->transfer_uid > 0 && erLhcoreClassModelTransfer::getCount(array('filter' => array('chat_id' => $chat->id))) > 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/transfered_chat.tpl.php'));?>
            <?php else : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/joined_chat.tpl.php'));?>
            <?php endif ?>
    	<?php endif;?>
    </h4>
    <?php endif;?>
    <?php elseif ($is_closed == true) : ?>
    <h4>	
    	<?php if ($theme !== false  && $theme->support_closed != '') : ?>
    	   <?php echo htmlspecialchars($theme->support_closed)?>
    	<?php else : ?>
    	   <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/closed_chat.tpl.php'));?>
    	<?php endif; ?>	
    </h4>
    <?php elseif ($is_online == true) : ?>
    <h4>
         <?php if ($chat->number_in_queue > 1) : ?><?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/you_a_number_in_queue.tpl.php'));?>
         <?php else : ?>
             <?php if ($theme !== false  && $theme->pending_join != '') : ?>
        	   <?php echo htmlspecialchars($theme->pending_join)?>
        	<?php else : ?>
        	   <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/pending_join.tpl.php'));?>
            <?php endif;?>
        <?php endif;?>
    </h4>
   
    <?php else : ?>
    <h4>
    <?php if ($theme !== false  && $theme->noonline_operators != '') : ?>
    	   <?php echo htmlspecialchars($theme->noonline_operators)?>
    	<?php else : ?>
    	   <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/no_logged_operators.tpl.php'));?>
        <?php endif;?>
    </h4>
<?php endif; ?>

