<?php if ($chat->product !== false) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Product')?></td>
        <td><?php echo htmlspecialchars($chat->product);?></td>
    </tr>
<?php endif;?>