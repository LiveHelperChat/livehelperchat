<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="bot-btn-list">
        <?php foreach ($metaMessage as $indexSub => $item) : ?>
            <li>
                <?php if ($item['type'] == 'url') : ?>
                <a <?php if (isset($item['content']['payload_message']) && $item['content']['payload_message'] != '') : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.enableVisitorEditor()"<?php endif;?> target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
                <i class="material-icons">&#xf3cc;</i>
                <?php elseif ($item['type'] == 'updatechat') : ?>
                <a data-no-change="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                <?php elseif ($item['type'] == 'trigger') : ?>
                <a data-no-change="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                <?php else : ?>
                <a data-no-change="true" onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                <?php endif?>

                <?php echo htmlspecialchars($item['content']['name']) ?>
                </a>
             </a>
        <?php endforeach; ?>
    </ul>
</div>