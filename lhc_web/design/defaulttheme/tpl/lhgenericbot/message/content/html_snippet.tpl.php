<?php foreach ($metaMessage as $messageCanned) : ?>
    <script>lhinst.sendHTML(<?php echo $msg['id']?>,<?php echo $messageCanned?>);</script>
<?php endforeach; ?>
