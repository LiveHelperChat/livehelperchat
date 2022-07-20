<?php $subMessages = erLhcoreClassBBCode::makeSubmessages($msgBody, isset($paramsMessageRender) ? $paramsMessageRender : array()); ?>
<?php foreach ($subMessages as $subMessage) : ?>
    <?php (in_array('nlt',$subMessage['flags'])) ? print '<br />' : ''; ?>
    <div class="msg-body<?php (in_array('img',$subMessage['flags']))? print ' msg-body-media' : ''?><?php (in_array('emoji',$subMessage['flags']))? print ' msg-body-emoji' : ''?>">
        <?php echo $subMessage['body']?>
        <?php if (isset($visitorRender) && $visitorRender == true && isset($metaMessageData)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_msg_body.tpl.php'));?>
        <?php elseif (isset($metaMessageData)): ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_msg_body_admin.tpl.php'));?>
        <?php endif; ?>
    </div>
    <?php (in_array('nl',$subMessage['flags'])) ? print '<br />' : ''; ?>
<?php endforeach; ?>