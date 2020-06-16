<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="bot-btn-list">
        <?php foreach ($metaMessage as $indexSub => $item) : ?>
            <li>
                <?php if ($item['type'] == 'url') : ?>
                <a <?php if (isset($item['content']['payload_message']) && $item['content']['payload_message'] != '') : ?>data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload_message'])?> onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.enableVisitorEditor()"<?php endif;?> rel="noreferrer" target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
                <i class="material-icons"><?php if (isset($react) && $react == true) : ?>&#xf106;<?php else : ?>open_in_new<?php endif; ?></i>
                <?php elseif ($item['type'] == 'updatechat') : ?>
                <a rel="noreferrer" data-no-change="true" data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload'])?> data-keep="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                <?php elseif ($item['type'] == 'trigger') : ?>
                <a rel="noreferrer" data-no-change="true" data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload']. '__' . md5($item['content']['name']))?> data-keep="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'] . '__' . md5($item['content']['name']))?>,<?php echo $messageId?>,$(this),true)'>
                <?php else : ?>
                <a rel="noreferrer" data-no-change="true" data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload']. '__' . md5($item['content']['name']))?> data-keep="true" onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'] . '__' . md5($item['content']['name']))?>,<?php echo $messageId?>,$(this),true)'>
                <?php endif?>

                <?php echo htmlspecialchars($item['content']['name']) ?>
                </a>
             </a>
        <?php endforeach; ?>
    </ul>
</div>