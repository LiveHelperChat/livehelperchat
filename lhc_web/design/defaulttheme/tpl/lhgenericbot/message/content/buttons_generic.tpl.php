<ul class="bot-btn-list">
    <?php foreach ($metaMessage as $item) : ?>
        <li>
            <?php if ($item['type'] == 'url') : ?>
            <a target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
            <i class="material-icons">open_in_new</i>
            <?php elseif ($item['type'] == 'updatechat') : ?>
            <li onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
            <?php else : ?>
            <a onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
            <?php endif?>
            <?php echo htmlspecialchars($item['content']['name']) ?>
            </a>
         </li>
    <?php endforeach; ?>
</ul>
