<div class="meta-auto-hide meta-message-<?php echo $messageId?>">
    <?php if (isset($metaMessage['verify_hash']) && md5(json_encode([$metaMessage['render_args'],$metaMessage['render_function']]) . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' )) === $metaMessage['verify_hash'] && is_callable($metaMessage['render_function'])) :
        $argsCall =  $metaMessage['render_args'];
        $argsCall[] = $messageId;
        $metaMessageContent = call_user_func_array($metaMessage['render_function'],$argsCall) ?>
        <?php echo $metaMessageContent?>
    <?php else : ?>
    <div class="msg-body"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Function is not callable')?> <?php echo htmlspecialchars(json_encode($metaMessage['render_function']))?></div>
    <?php endif;?>
</div>