<?php if (is_callable($metaMessage['render_function'])) : $metaMessage = call_user_func_array($metaMessage['render_function'], $metaMessage['render_args']) ?>
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/quick_replies.tpl.php'));?>
<?php else : ?>
<div class="msg-body"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Could not execute')?> <?php echo htmlspecialchars($metaMessage['render_function'])?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','with args')?> <?php echo htmlspecialchars($metaMessage['render_args'])?></div>
<?php endif; ?>
