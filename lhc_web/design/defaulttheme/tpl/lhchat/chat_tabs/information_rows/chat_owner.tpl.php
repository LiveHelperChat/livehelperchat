<tr>
    <td>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?>
    </td>
    <td>
        <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
            <?php echo htmlspecialchars($user->name.' '.$user->surname)?>
        <?php endif; ?>
    </td>
</tr>