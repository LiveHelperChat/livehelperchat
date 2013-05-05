<?php if ($is_activated == true) : ?>
     <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Support staff member has joined this chat'); ?>
<?php elseif ($is_closed == true) : ?>
	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Support staff member has closed this chat'); ?>
<?php elseif ($is_online == true) : ?>
     <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending support staff member to join, you can write your questions, as soon support staff member confirm this chat, he will get your messages'); ?>
<?php else : ?>
     <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged members, but you can leave your messages'); ?>
<?php endif; ?>

