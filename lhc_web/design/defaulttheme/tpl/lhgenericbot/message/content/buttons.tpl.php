<?php if (is_callable($metaMessage['render_function'])) : $metaMessage = call_user_func_array($metaMessage['render_function'], $metaMessage['render_args']) ?>
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/quick_replies.tpl.php'));?>
<?php else : ?>
<div class="msg-body">Could not call <?php echo htmlspecialchars($metaMessage['render_function'])?> with args <?php echo htmlspecialchars($metaMessage['render_args'])?></div>
<?php endif; ?>
