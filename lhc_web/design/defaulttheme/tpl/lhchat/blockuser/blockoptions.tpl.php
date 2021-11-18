<label><input type="checkbox" name="btype[]" checked="checked" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_IP?>"> IP</label></br>
<?php if (!isset($chat) || $chat->nick != 'Visitor') : ?>
<label><input type="checkbox" name="btype[]" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_NICK?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick')?>&nbsp;<?php if (isset($chat)) : ?><small>(<?php echo htmlspecialchars($chat->nick)?>)</small><?php endif; ?></label>
</br>
<label><input type="checkbox" name="btype[]" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_NICK_DEP?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick and Department')?>&nbsp;<?php if (isset($chat)) : ?><small>(<?php echo htmlspecialchars($chat->nick)?> && <?php echo htmlspecialchars($chat->department_name)?>)</small><?php endif; ?></label></br>
<?php endif; ?>
<label><input type="checkbox" name="btype_email" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','E-mail')?></label>
