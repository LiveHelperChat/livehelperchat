<?php foreach (erLhcoreClassChat::getOnlineUsers(array($user_id), $user_filter) as $key => $user) : ?>
    <label><input type="radio" name="TransferTo<?php echo $chat->id?>" value="<?php echo $user['id']?>" <?php echo $key == 0 ? 'checked="checked"' : ''?>> <?php echo htmlspecialchars($user['name'])?> <?php echo htmlspecialchars($user['surname'])?></label><br/>
<?php endforeach; ?>