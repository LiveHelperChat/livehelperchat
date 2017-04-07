<div class="canned-suggester">
    <ul class="list-unstyled canned-list" id="canned-hash-<?php echo $chat->id?>">
        <?php foreach (erLhcoreClassModelCannedMsgTagLink::formatSuggester($tags,array('chat' => $chat, 'user' => erLhcoreClassUser::instance()->getUserData())) as $item) : ?>
            <li><a href="#">[<?php echo htmlspecialchars($item['tag']->cnt)?>] <?php echo htmlspecialchars($item['tag']->tag)?> &raquo;</a>
                <ul class="list-unstyled">
                    <?php foreach ($item['messages'] as $message) : ?>
                        <li><span class="mr-0 left-return">&laquo;&nbsp;</span><span class="canned-msg" data-msg="<?php echo htmlspecialchars($message->msg_to_user)?>"><?php echo htmlspecialchars($message->message_title)?> &raquo;</span></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
    <div id="canned-hash-current-<?php echo $chat->id?>" class="current-hash-content"></div>
</div>