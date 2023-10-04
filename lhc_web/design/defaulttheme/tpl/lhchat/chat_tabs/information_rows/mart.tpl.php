<?php if ($chat->mart > 0) : ?>
<div class="col-6 pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Max response time (agent)')?> - <?php echo erLhcoreClassChat::formatSeconds($chat->mart); ?></div>
<?php endif; ?>