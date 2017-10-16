<?php if ($chat->user_closed_ts > 0 && $chat->user_status == 1) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','User left')?></td>
        <td><?php echo $chat->user_closed_ts_front?></td>
    </tr>
<?php endif;?>