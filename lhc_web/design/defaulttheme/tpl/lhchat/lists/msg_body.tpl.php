<?php $subMessages = erLhcoreClassBBCode::makeSubmessages($msgBody); ?>
<?php foreach ($subMessages as $subMessage) : ?>
    <div class="msg-body<?php (in_array('img',$subMessage['flags']))? print ' msg-body-media' : ''?><?php (in_array('emoji',$subMessage['flags']))? print ' msg-body-emoji' : ''?>">
        <?php echo $subMessage['body']?>
    </div>
<?php endforeach; ?>