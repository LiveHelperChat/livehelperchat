<tr>
    <td>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?>
    </td>
    <td id="chat-owner-<?php echo $chat->id?>" user-id="<?php echo $chat->user_id?>">
        <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
            <?php echo htmlspecialchars($user->name.' '.$user->surname)?>
        <?php endif; ?>
    </td>
</tr>