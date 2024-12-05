<?php if (is_array($metaMessage)) : ?>

    <?php if (isset($metaMessage['debug']) && $metaMessage['debug']) : ?>
        <div class="msg-body msg-body-media">
            <pre class="blockquote blockquote-code" style="resize: vertical; height: 100px; min-height: 100px"><?php echo htmlspecialchars($metaMessage['content']);?></pre>
        </div>
    <?php else : ?>
        <?php $msgBody = '[html]'.$metaMessage['content'].'[/html]'; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    <?php endif; ?>

<?php endif; ?>
