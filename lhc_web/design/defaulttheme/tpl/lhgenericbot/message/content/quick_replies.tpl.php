<div class="meta-message meta-message-<?php echo $messageId?>">
    <ul class="quick-replies list-inline meta-auto-hide">
    <?php foreach ($metaMessage as $item) : ?>
        <li>
            <?php if ($item['type'] == 'url') : ?>
                <a class="btn btn-xs btn-info" target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
                    <i class="material-icons">open_in_new</i>
             <?php elseif ($item['type'] == 'trigger') : ?>
                <a class="btn btn-xs btn-info" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>)'>
            <?php elseif ($item['type'] == 'updatechat') : ?>
                <a class="btn btn-xs btn-info" onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>)'>
            <?php else : ?>
                <a class="btn btn-xs btn-info" onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>)'>
            <?php endif?>

            <?php echo htmlspecialchars($item['content']['name'])?></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

