<?php if ($chat->auto_responder_id > 0) : ?>
    <div class="col-6 pb-1">
        <div>
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder')?>" class="material-icons">quick_phrases</i><?php if ($chat->auto_responder !== false && $chat?->auto_responder?->auto_responder != '') : ?><?php echo htmlspecialchars($chat->auto_responder->auto_responder);?><?php else: ?><span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder was assigned')?></span><?php endif;?>
        </div>
    </div>
<?php endif; ?>