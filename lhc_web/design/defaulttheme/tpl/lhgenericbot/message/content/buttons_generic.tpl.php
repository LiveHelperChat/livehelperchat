<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="bot-btn-list">
        <?php foreach ($metaMessage as $indexSub => $item) : ?>
            <li>
                <?php if ($item['type'] == 'url') : ?>
                <a target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
                <i class="material-icons">open_in_new</i>
                <?php elseif ($item['type'] == 'updatechat') : ?>
                <a onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
                <?php elseif ($item['type'] == 'trigger') : ?>
                <a onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
                <?php else : ?>
                <a onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
                <?php endif?>

                <?php echo htmlspecialchars($item['content']['name']) ?>
                </a>
             </a>
        <?php endforeach; ?>
    </ul>
</div>