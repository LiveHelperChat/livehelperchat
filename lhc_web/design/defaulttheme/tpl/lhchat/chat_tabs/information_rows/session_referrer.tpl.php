<?php if (!empty($chat->session_referrer)) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Came from')?></td>
        <td><div class="page-url"><span><?php echo $chat->session_referrer != '' ? '<a target="_blank" rel="noopener" title="' . htmlspecialchars($chat->session_referrer) . '" href="' . htmlspecialchars($chat->session_referrer) . '">'.htmlspecialchars($chat->session_referrer).'</a>' : ''?></span></div></td>
    </tr>
<?php endif;?>