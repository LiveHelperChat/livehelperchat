<?php if ($is_online == true) : ?>
    <?php if ($is_activated == true) : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Support staff member have joined this chat'); ?>
    <?php else : ?>
         <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending support staff member to join, you can write your questions, as soon support staff member confirm this chat, he will get your messages'); ?>
    <?php endif; ?>
<?php else : ?>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged members, but you can leave your messages.'); ?>
<?php endif; ?>