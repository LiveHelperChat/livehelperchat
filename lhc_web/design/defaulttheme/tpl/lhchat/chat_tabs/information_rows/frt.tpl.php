<?php if ($chat->frt > 0) : ?>
<div class="col-6 pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','First response time (agent)')?> - <?php echo erLhcoreClassChat::formatSeconds($chat->frt); ?></div>
<?php endif; ?>