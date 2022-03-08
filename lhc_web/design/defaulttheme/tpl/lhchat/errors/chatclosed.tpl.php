<h4>
    <?php if (isset($chat)) {
        $chatVariables = $chat->chat_variables_array;
        if (erLhcoreClassModelChatBlockedUser::isBlocked(array('online_user_id' => $chat->online_user_id, 'country_code' => $chat->country_code, 'ip' => $chat->ip, 'dep_id' => $chat->dep_id, 'nick' => $chat->nick, 'email' => $chat->email)) || (isset($chatVariables['lhc_ds']) && (int)$chatVariables['lhc_ds'] == 0)) {
            echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','At this moment you can contact us via email only. Sorry for the inconveniences.');
            $blocked = true;
        }
    } ?>
    <?php if (!isset($blocked)) : ?>
         <?php if ($theme !== false && $theme->support_closed != '')  : ?>
           <?php echo htmlspecialchars($theme->support_closed) ?>
        <?php  else  : ?>
           <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support staff member has closed this chat')?>
        <?php endif;?>
    <?php endif;?>
</h4>

<?php if ($modeembed == 'widget') : ?>
<input type="button" class="btn btn-secondary btn-sm mb-1" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
<?php endif;?>
