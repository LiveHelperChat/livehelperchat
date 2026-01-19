<?php if (is_array($metaMessage)) : ?>

    <?php if (isset($metaMessage['debug']) && $metaMessage['debug']) : ?>
        <div class="msg-body msg-body-media">
            <?php $debugData = json_decode($metaMessage['content'],true);?>
            <?php if (isset($debugData['params_request'])) : ?>
                <button class="btn btn-xs btn-outline-secondary" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('audit/copycurl')?>/<?php print (is_object($msg) ? $msg->id : $msg['id'])?>'});"  type="button">Copy as CURL</button>
            <?php endif; ?>
            <pre class="blockquote blockquote-code" style="resize: vertical; height: 100px; min-height: 100px"><?php
                if (isset($debugData['params_request'])) {
                    if (isset($debugData['params_request']['body']) && !is_array($debugData['params_request']['body'])){
                        $bodyJSON = json_decode(str_replace(["\n","\r\n"],"",$debugData['params_request']['body']),true);
                        if (is_array($bodyJSON)) {
                            $debugData['params_request']['body_parsed'] = $bodyJSON;
                        }
                    }
                    if (isset($debugData['stream']['content_raw']) && !is_array($debugData['stream']['content_raw'])){
                        $bodyJSON = json_decode(str_replace(["\n","\r\n"],"",$debugData['stream']['content_raw']),true);
                        if (is_array($bodyJSON)) {
                            $debugData['stream']['content_raw_parsed'] = $bodyJSON;
                        }
                    }
                    echo htmlspecialchars(json_encode($debugData,JSON_PRETTY_PRINT));
                } else {
                    echo htmlspecialchars($metaMessage['content']);
                }?></pre>

        </div>
    <?php else : ?>
        <?php $msgBody = '[html]'.str_replace(["\r","\n"],["",""],$metaMessage['content']).'[/html]'; $paramsMessageRender = array('msg_body_class' => (isset($type) && $type === 'debug' ? 'bg-transparent text-muted' : ''), 'sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    <?php endif; ?>

<?php endif; ?>
