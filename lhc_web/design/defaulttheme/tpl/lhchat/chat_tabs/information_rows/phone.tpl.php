<?php if (!empty($chat->phone)) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Phone')?></td>
        <td><?php echo htmlspecialchars($chat->phone)?></td>
    </tr>
<?php endif;?>