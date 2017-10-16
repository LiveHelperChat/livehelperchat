<?php if ( !empty($chat->user_tz_identifier) ) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Time zone')?></td>
        <td><?php echo htmlspecialchars($chat->user_tz_identifier)?>, <?php echo htmlspecialchars($chat->user_tz_identifier_time)?></td>
    </tr>
<?php endif;?>