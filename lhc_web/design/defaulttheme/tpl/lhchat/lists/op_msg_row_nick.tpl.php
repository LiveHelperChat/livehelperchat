<span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>">
    <?php if (
        isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) &&
        $theme->bot_configuration_array['bubble_style_profile'] == 1 &&
        $msg['user_id'] == -2 &&
        $chat->bot instanceof erLhcoreClassModelGenericBotBot &&
        $chat->bot->has_photo
    ) : ?>
        <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="chat-operators mi-fs15 mr-0">
            <img class="profile-msg-pic" src="<?php echo $chat->bot->photo_path?>" alt="">
        </i>
    <?php elseif ($msg['user_id'] > 0 && isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1 &&
        ($userMessage = erLhcoreClassModelUser::fetch($msg['user_id'],true)) && $userMessage instanceof erLhcoreClassModelUser && $userMessage->has_photo
    ) : ?>
            <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="chat-operators mi-fs15 mr-0">
                <img class="profile-msg-pic" src="<?php echo $userMessage->photo_path?>" alt="">
            </i>
    <?php elseif (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1 && $theme->operator_image_url !== false) : ?>
        <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="chat-operators mi-fs15 mr-0">
            <img class="profile-msg-pic" src="<?php echo $theme->operator_image_url?>" alt="">
        </i>
    <?php else : ?>
        <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="material-icons chat-operators mi-fs15 mr-0"><?php if (isset($react) && $react == true) : ?>&#xf10d;<?php else : ?>account_box<?php endif; ?> </i>
        <span class="op-nick-title"><?php echo htmlspecialchars($msg['name_support'])?></span>
    <?php endif; ?>
</span>