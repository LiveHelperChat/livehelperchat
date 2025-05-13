<?php if (is_array($metaMessage)) : ?>

    <?php if (isset($metaMessage['debug']) && $metaMessage['debug']) : ?>
        <div class="msg-body msg-body-media">
            <?php $debugData = json_decode($metaMessage['content'],true);?>
            <?php if (isset($debugData['params_request'])) : ?>
                <button class="btn btn-xs btn-outline-secondary" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('audit/copycurl')?>/<?php print (is_object($msg) ? $msg->id : $msg['id'])?>'});"  type="button">Copy as CURL</button>
            <?php endif; ?>
            <pre class="blockquote blockquote-code" style="resize: vertical; height: 100px; min-height: 100px"><?php echo htmlspecialchars($metaMessage['content']);?></pre>
        </div>
    <?php else : ?>
        <?php $msgBody = '[html]'.str_replace(["\r","\n"],["",""],$metaMessage['content']).'[/html]'; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    <?php endif; ?>

<?php endif; ?>
