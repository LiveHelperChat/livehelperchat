<?php if ( $chat->department !== false ) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Department')?></td>
        <td><?php if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST) : ?><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','This is offline message')?>" class="material-icons">mail</i> <?php endif?><?php echo htmlspecialchars($chat->department);?></td>
    </tr>
<?php endif;?>