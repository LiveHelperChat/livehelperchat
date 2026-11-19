<span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>">
    <i title="<?php echo htmlspecialchars($chat->nick)?>" class="vis-icon-hld material-icons chat-operators mi-fs15 me-0"><?php if (isset($react) && $react == true) : ?>&#xf104;<?php else : ?>face<?php endif; ?></i>
    <span class="user-nick-title"><?php echo htmlspecialchars($chat->nick)?></span>
</span>