<?php if ($is_activated == true || $is_proactive_based == true) : ?>
     <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has joined this chat'); ?>
<?php elseif ($is_closed == true) : ?>
	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has closed this chat'); ?>
<?php elseif ($is_online == true) : ?>
     <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending a support staff member to join, you can write your questions, and as soon as a support staff member confirms this chat, he will get your messages'); ?>
<?php else : ?>
     <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged in support staff members, but you can leave your messages'); ?>
<?php endif; ?>

