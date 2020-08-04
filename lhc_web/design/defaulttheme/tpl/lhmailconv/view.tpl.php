<div id="chat-id-mc<?php echo $item->id?>"></div>

<script>
    ee.emitEvent('mailChatTabLoaded', ['mc<?php echo $item->id?>','',true]);
</script>

<div>
<?php /*foreach (erLhcoreClassModelMailconvMessage::getList(['sort' => 'udate ASC','filter' => ['conversation_id' => $item->id]]) as $message) : ?>
    <div class="row border-top py-2">
        <div class="col-12">
            <div><span class="font-weight-bold">Subject:</span> <?php echo htmlspecialchars($message->subject)?>, <span class="font-weight-bold">From:</span> <?php echo htmlspecialchars($message->from_name)?> &lt;<?php echo htmlspecialchars($message->from_address)?>&gt; </div>
        </div>
        <div class="col-12">
            <?php echo nl2br(htmlspecialchars($message->alt_body)); ?>
        </div>
    </div>
<?php endforeach;*/ ?>
</div>