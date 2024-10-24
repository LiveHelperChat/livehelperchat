<?php if (is_array($metaMessage)) : ?>

    <?php if (isset($metaMessage['debug']) && $metaMessage['debug']) : ?>

    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhaudit','see_audit_system')) : ?>
        <div class="msg-body msg-body-media">
            <pre class="blockquote blockquote-code" style="resize: vertical; height: 100px; min-height: 100px"><?php echo htmlspecialchars($metaMessage['content']);?></pre>
        </div>
    <?php else : ?>
        <div class="msg-body msg-body-media">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','No permission to see audit system messages.')?>
        </div>
    <?php endif; ?>

    <?php else : ?>
        <?php $msgBody = '[html]'.$metaMessage['content'].'[/html]'; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    <?php endif; ?>

<?php endif; ?>
