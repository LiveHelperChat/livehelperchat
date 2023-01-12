<h6 class="fw-bold"><i class="material-icons">face</i>
    <?php if (isset($chat->chat_variables_array['nick_secure']) && $chat->chat_variables_array['nick_secure'] == true) : ?>
        <i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Passed as encrypted variable')?>">enhanced_encryption</i>
    <?php endif; ?>
    <?php if ($chat->nick != 'Visitor') : ?>
        <?php echo htmlspecialchars($chat->nick)?>
    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?>
    <?php endif; ?>
</h6>