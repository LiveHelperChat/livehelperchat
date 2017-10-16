<?php if (!empty($chat->email)) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?></td>
        <td><a href="mailto:<?php echo $chat->email?>"><?php echo $chat->email?></a></td>
    </tr>
<?php endif;?>