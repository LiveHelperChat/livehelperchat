<?php $subMessages = erLhcoreClassBBCode::makeSubmessages($msgBody, isset($paramsMessageRender) ? $paramsMessageRender : array()); ?>
<?php foreach ($subMessages as $subMessage) : ?>
    <?php (in_array('nlt',$subMessage['flags'])) ? print '<br />' : ''; ?>
    <div class="msg-body<?php (in_array('img',$subMessage['flags']))? print ' msg-body-media' : ''?><?php (in_array('emoji',$subMessage['flags']))? print ' msg-body-emoji' : ''?>">
        <?php echo $subMessage['body']?>
    </div>
    <?php (in_array('nl',$subMessage['flags'])) ? print '<br />' : ''; ?>
<?php endforeach; ?>