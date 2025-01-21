<?php if ((isset($init_sync) && $init_sync === true && isset($chat) && isset($msg['id']) && $msg['id'] === $chat->last_msg_id) || (isset($async_call) && $async_call == true) || (isset($chat_started_now) && isset($chat_started_now) == true && (!isset($msg['id']) || !isset($old_msg_id) || $msg['id'] > $old_msg_id))) : if (isset($chat_started_now) && isset($chat_started_now) == true && (!isset($metaMessage['untill_message']) || $metaMessage['untill_message'] == false)) {$hideNextMessages = true;}?>
<script data-bot-action="lhinst.setDelay" data-bot-args='{<?php

$exposeDelays = array();
for ($i = 1; $i <= 3; $i++) {
    if (isset($metaMessage['delay_expose_'.$i]) && (int)$metaMessage['delay_expose_'.$i] > 0) {
        $exposeDelays['delay_expose_'.$i] = (int)$metaMessage['delay_expose_'.$i];
        $exposeDelays['delay_expose_text_'.$i] = $metaMessage['delay_expose_text_'.$i];
    }
}

if (!empty($exposeDelays)) {
    $exposeDelays['stime'] = $msg['time'] + (isset($metaMessage['delay']) ? (int)$metaMessage['delay'] : 0);
    echo "\"expose_delays\":",json_encode($exposeDelays, JSON_HEX_APOS),",";
}

?>"delay":<?php echo isset($metaMessage['delay']) ? (int)$metaMessage['delay']  : 0 ?>, "untill_message" : <?php echo (isset($metaMessage['untill_message']) && $metaMessage['untill_message'] == true) ? 'true' : 'false' ?>,"id" : <?php echo $msg['id']?>, "duration" : <?php echo isset($metaMessage['duration']) ? (int)$metaMessage['duration'] : 0?>}'>
(function(){
    var args = {'delay':<?php echo isset($metaMessage['delay']) ? (int)$metaMessage['delay']  : 0 ?>, 'untill_message' : <?php echo (isset($metaMessage['untill_message']) && $metaMessage['untill_message'] == true) ? 'true' : 'false' ?>,'id' : <?php echo $msg['id']?>, 'duration' : <?php echo isset($metaMessage['duration']) ? (int)$metaMessage['duration'] : 0?>};
    lhinst.setDelay(args);
})();
</script>
<div class="msg-body<?php if (!isset($chat_started_now) || $chat_started_now == false) : ?> hide<?php endif;?>"><?php if (isset($metaMessage['text']) && !empty($metaMessage['text'])) : ?><?php echo htmlspecialchars($metaMessage['text'])?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Typing...');?><?php endif; ?></div>
<?php endif; ?>