<?php if ($chat->chat_duration > 0) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?></td>
        <td><?php echo $chat->chat_duration_front?></td>
    </tr>
<?php endif;?>