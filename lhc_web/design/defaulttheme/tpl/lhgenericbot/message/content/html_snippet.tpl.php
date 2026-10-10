<?php foreach ($metaMessage as $messageCanned) : ?>
    <script>lhinst.sendHTML(<?php echo (int)$msg['id']?>,{'type':'msg','id':<?php echo (int)$messageCanned?>});</script>
<?php endforeach; ?>
