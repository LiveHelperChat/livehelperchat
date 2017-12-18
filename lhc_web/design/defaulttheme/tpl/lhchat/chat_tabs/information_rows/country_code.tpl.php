<?php if ( !empty($chat->country_code) ) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Country')?></td>
        <td><img src="<?php echo erLhcoreClassDesign::design('images/flags')?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" /> <?php echo htmlspecialchars($chat->chat_locale)?></td>
    </tr>
<?php endif;?>