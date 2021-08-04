<label><input type="checkbox" name="btype[]" checked="checked" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_IP?>"> IP</label></br>
<label><input type="checkbox" name="btype[]" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_NICK?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick')?></label></br>
<label><input type="checkbox" name="btype[]" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_NICK_DEP?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick and Department')?></label></br>
<label><input type="checkbox" name="btype_email" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','E-mail')?></label>
