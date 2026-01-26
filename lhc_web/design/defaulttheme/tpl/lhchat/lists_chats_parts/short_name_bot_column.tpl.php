<?php if ($chat->bot) : ?>
    <?php if (!empty($chat->bot->short_name)) : ?>
        &nbsp;[<?php echo htmlspecialchars($chat->bot->short_name); ?>]
    <?php else : ?>
        &nbsp;[<?php if ($chat->status != erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?><span class="material-icons">android</span><?php endif;?><?php echo htmlspecialchars($chat->gbot_id); ?>]
    <?php endif; ?>
<?php endif;?>