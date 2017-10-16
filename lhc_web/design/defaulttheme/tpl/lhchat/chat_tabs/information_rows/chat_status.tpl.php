<?php if ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat between operators, chat initializer')?></td>
        <td><?php echo htmlspecialchars($chat->nick)?></td>
    </tr>
<?php endif;?>