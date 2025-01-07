<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="bot-btn-list">
        <?php foreach ($metaMessage as $indexSub => $item) : ?>
            <li>
                <?php if ($item['type'] == 'url') : ?>
                <a tabindex="0" <?php if (isset($item['content']['payload_message']) && $item['content']['payload_message'] != '') : ?>data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload_message'])?> onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php endif;?> class="btn-url" rel="noreferrer" target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
                <i class="material-icons"><?php if (isset($react) && $react == true) : ?>&#xf106;<?php else : ?>open_in_new<?php endif; ?></i>
                <?php elseif ($item['type'] == 'updatechat') : ?>

                <?php if ($item['content']['payload'] == 'minimizeWidget') : ?>
                    <a rel="noreferrer" tabindex="0" data-bot-action="execute-js" data-no-change="true" type="button" class="btn-link action-image" data-id="<?php echo $messageId?>" data-bot-emit-parent="true" data-no-msg="true" data-bot-emit="minWidget" onclick='lhinst.executeJS();'>
                <?php else : ?>
                    <a rel="noreferrer" tabindex="0" data-no-change="true" class="btn-link action-image" <?php if (isset($item['content']['no_name']) && $item['content']['no_name'] == true) : ?>data-no-msg="true"<?php endif; ?> data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload'])?> data-keep="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                <?php endif; ?>

                <?php elseif ($item['type'] == 'trigger') : ?>
                <a rel="noreferrer" tabindex="0" data-no-change="true" class="btn-link action-image <?php if (isset($metaMessageData['ch']) && in_array(md5($item['content']['name']),$metaMessageData['ch'])) : ?>visited<?php endif;?>" <?php if (isset($item['content']['no_name']) && $item['content']['no_name'] == true) : ?>data-no-msg="true"<?php endif; ?> data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload']. '__' . md5($item['content']['name']))?> data-keep="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'] . '__' . md5($item['content']['name']))?>,<?php echo $messageId?>,$(this),true)'>
                <?php else : ?>
                <a rel="noreferrer" tabindex="0" data-no-change="true" class="btn-link action-image <?php if (isset($metaMessageData['ch']) && in_array(md5($item['content']['name']),$metaMessageData['ch'])) : ?>visited<?php endif;?>" <?php if (isset($item['content']['no_name']) && $item['content']['no_name'] == true) : ?>data-no-msg="true"<?php endif; ?> data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($item['content']['payload']. '__' . md5($item['content']['name']))?> data-keep="true" onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'] . '__' . md5($item['content']['name']))?>,<?php echo $messageId?>,$(this),true)'>
                <?php endif?>

                <?php echo htmlspecialchars($item['content']['name']) ?>
                </a>
             </a>
        <?php endforeach; ?>
    </ul>
</div>