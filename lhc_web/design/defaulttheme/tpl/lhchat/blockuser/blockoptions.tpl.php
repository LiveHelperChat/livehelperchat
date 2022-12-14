<label><input type="checkbox" name="btype[]" checked="checked" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_IP?>"> IP<?php if (isset($chat) && isset($chat->ip)) : ?>&nbsp;<small ng-non-bindable>(<?php
       if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','seeip')) {
           echo htmlspecialchars($chat->ip);
       } else {
           echo htmlspecialchars(preg_replace(
               [
                   '/(\.\d+){2}$/',
                   '/(:[\da-f]*){2,4}$/'
               ],
               [
                   '.XXX.XXX',
                   ':XXXX:XXXX:XXXX:XXXX'
               ],
               $chat->ip
           ));
       }
       ?>)</small><?php endif;?></label></br>
<?php if (!isset($chat) || $chat->nick != 'Visitor') : ?>
<label ng-non-bindable><input type="checkbox" name="btype[]" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_NICK?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick')?>&nbsp;<?php if (isset($chat)) : ?><small>(<?php echo htmlspecialchars($chat->nick)?>)</small><?php endif; ?></label>
</br>
<label ng-non-bindable><input type="checkbox" name="btype[]" value="<?php echo erLhcoreClassModelChatBlockedUser::BLOCK_NICK_DEP?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick and Department')?>&nbsp;<?php if (isset($chat)) : ?><small>(<?php echo htmlspecialchars($chat->nick)?> && <?php echo htmlspecialchars($chat->department_name)?>)</small><?php endif; ?></label></br>
<?php endif; ?>
<?php if (isset($chat) && isset($chat->online_user_id)) : ?>
<label><input type="checkbox" name="btype_online_user" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Online user (cookie)')?></label></br>
<?php endif; ?>
<label><input type="checkbox" name="btype_email" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','E-mail')?></label>
