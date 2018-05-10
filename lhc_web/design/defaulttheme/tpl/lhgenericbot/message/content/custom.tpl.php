<div class="meta-auto-hide meta-message-<?php echo $messageId?>">
    <?php if (is_callable($metaMessage['render_function'])) :
        $argsCall =  $metaMessage['render_args'];
        $argsCall[] = $messageId;
        $metaMessageContent = call_user_func_array($metaMessage['render_function'],$argsCall) ?>
        <?php echo $metaMessageContent?>
    <?php else : ?>
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Function is not callable')?> <?php echo htmlspecialchars($metaMessage['render_function'])?></p>
    <?php endif; ?>
</div>