<?php if ($chat->wait_time > 0) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Waited')?></td>
        <td><?php echo $chat->wait_time_front?> </td>
    </tr>
<?php endif;?>