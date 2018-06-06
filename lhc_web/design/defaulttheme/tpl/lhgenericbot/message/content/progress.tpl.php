<?php
if (is_callable($metaMessage['method'])) :
$jsExecute = call_user_func_array($metaMessage['method'],array($metaMessage['args'])); ?>
<script>
    function interval_function_<?php echo $msg['id']?>(){
        $.getJSON(WWW_DIR_JAVASCRIPT + "<?php echo $jsExecute['url']?>/(id)/<?php echo $msg['id']?>",<?php echo json_encode($jsExecute['args'])?>, function(data) {
            if (data.error == false) {
                $('#chat-progress-status').removeClass('hide');
                $('#chat-progress-status').html(data.result);
            } else{
                $('#chat-progress-status').html(data.result);
                setTimeout(function() {
                    $('#chat-progress-status').addClass('hide');
                    clearInterval(interval_<?php echo $msg['id']?>);
                },2000);
            }
        }).fail(function() {

        });
    }
    var interval_<?php echo $msg['id']?> = setInterval(function () {
        interval_function_<?php echo $msg['id']?>();
    },<?php echo $metaMessage['interval']?>*1000);
    interval_function_<?php echo $msg['id']?>();
</script>
<?php endif; ?>
