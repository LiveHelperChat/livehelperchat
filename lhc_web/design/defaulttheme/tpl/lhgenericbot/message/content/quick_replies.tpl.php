<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="quick-replies list-inline meta-auto-hide">
    <?php foreach ($metaMessage as $item) : $disabledButton = isset($item['content']['disabled']) && $item['content']['disabled'] == true;?>
        <li>
            <?php if ($item['type'] == 'url') : ?>
                <a class="btn btn-xs btn-info btn-bot" target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
                    <i class="material-icons">open_in_new</i><?php echo htmlspecialchars($item['content']['name'])?></a>
             <?php elseif ($item['type'] == 'trigger') : ?>
                <button type="button" class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> <?php if ($disabledButton == false) : ?>onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this))'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button>
            <?php elseif ($item['type'] == 'updatechat') : ?>
                <?php if ($item['content']['payload'] == 'subscribeToNotifications') : ?>
                    <button type="button" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == false) : ?>onclick='notificationsLHC.sendNotification()'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button>
                <?php else : ?>
                    <button type="button" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == false) : ?>onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this))'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button>
                <?php endif; ?>
            <?php else : ?>
                <button type="button" <?php if ($disabledButton == true) : ?>disabled="disabled"<?php endif;?> class="btn btn-xs btn-info btn-bot" <?php if ($disabledButton == false) : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this))'<?php endif;?>><?php echo htmlspecialchars($item['content']['name'])?></button>
            <?php endif?>

        </li>
    <?php endforeach; ?>
    </ul>
</div>

