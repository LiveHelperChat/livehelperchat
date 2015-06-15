<?php if ($is_activated == true || $is_proactive_based == true) : ?>
    <?php if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && ($user = $chat->user) !== false) : ?>
    	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php'));?>
    <?php elseif ($is_proactive_based == true) : ?>
    <h4>
    	<?php if ($theme !== false  && $theme->support_joined != '') : ?>
    	   <?php echo htmlspecialchars($theme->support_joined)?>
    	<?php else : ?>
    	   <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has joined this chat'); ?>
    	<?php endif;?>
    </h4>
    <?php endif;?>
    <?php elseif ($is_closed == true) : ?>
    <h4>	
    	<?php if ($theme !== false  && $theme->support_closed != '') : ?>
    	   <?php echo htmlspecialchars($theme->support_closed)?>
    	<?php else : ?>
    	   <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has closed this chat'); ?>
    	<?php endif; ?>	
    </h4>
    <?php elseif ($is_online == true) : ?>
     
    <h4>
         <?php if ($chat->number_in_queue > 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','You are number')?> <b><?php echo $chat->number_in_queue?></b> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','in the queue. Please wait...')?>
         <?php else : ?>
             <?php if ($theme !== false  && $theme->pending_join != '') : ?>
        	   <?php echo htmlspecialchars($theme->pending_join)?>
        	<?php else : ?>
               <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending a support staff member to join, you can write your questions, and as soon as a support staff member confirms this chat, he will get your messages'); ?>
            <?php endif;?>
        <?php endif;?>
    </h4>
   
    <?php else : ?>
    <h4>
    <?php if ($theme !== false  && $theme->noonline_operators != '') : ?>
    	   <?php echo htmlspecialchars($theme->noonline_operators)?>
    	<?php else : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged in support staff members, but you can leave your messages'); ?>
        <?php endif;?>
    </h4>
<?php endif; ?>

