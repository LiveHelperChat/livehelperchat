<?php if ( !empty($chat->city) ) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','City')?></td>
        <td><?php echo htmlspecialchars($chat->city);?></td>
    </tr>
<?php endif;?>