<?php if (isset($metaMessage['verify_hash']) && md5(json_encode([$metaMessage['render_args'],$metaMessage['render_function']]) . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' )) == $metaMessage['verify_hash'] && is_callable($metaMessage['render_function'])) : $metaMessage = call_user_func_array($metaMessage['render_function'], $metaMessage['render_args']) ?>
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/quick_replies.tpl.php'));?>
<?php else : ?>
<div class="msg-body"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Could not execute')?> <?php echo htmlspecialchars(json_encode($metaMessage['render_function']))?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','with args')?> <?php echo htmlspecialchars(json_encode($metaMessage['render_args']))?></div>
<?php endif; ?>
