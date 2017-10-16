<?php if (!empty($chat->referrer)) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Page')?></td>
        <td><div class="page-url"><span><?php echo $chat->referrer != '' ? '<a target="_blank" rel="noopener" title="' . htmlspecialchars($chat->referrer) . '" href="' .htmlspecialchars($chat->referrer). '">'.htmlspecialchars($chat->referrer).'</a>' : ''?></span></div></td>
    </tr>
<?php endif;?>