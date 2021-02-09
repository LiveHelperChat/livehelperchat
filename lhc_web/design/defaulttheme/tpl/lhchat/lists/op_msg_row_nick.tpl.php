<span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>">
    <?php if (
        isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) &&
        $theme->bot_configuration_array['bubble_style_profile'] == 1 &&
        $msg['user_id'] == -2 &&
        $chat->bot instanceof erLhcoreClassModelGenericBotBot &&
        $chat->bot->has_photo_avatar
    ) : ?>
        <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="chat-operators mi-fs15 mr-0">
            <?php if ($chat->bot->has_photo) : ?>
            <img class="profile-msg-pic" src="<?php echo $chat->bot->photo_path?>" alt="">
            <?php else : ?>
            <img class="profile-msg-pic" src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($chat->bot->avatar)?>" alt="" />
            <?php endif; ?>
        </i>
    <?php elseif ($msg['user_id'] > 0 && isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1 &&
        ($userMessage = erLhcoreClassModelUser::fetch($msg['user_id'],true)) && $userMessage instanceof erLhcoreClassModelUser && $userMessage->has_photo_avatar
    ) : ?>
            <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="chat-operators mi-fs15 mr-0">
                <?php if ($userMessage->has_photo) : ?>
                    <img class="profile-msg-pic" src="<?php echo $userMessage->photo_path?>" alt="">
                <?php else : ?>
                    <img class="profile-msg-pic" src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($userMessage->avatar)?>" alt="" />
                <?php endif; ?>
            </i>
    <?php elseif (isset($theme) && $theme !== false && isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1 && $theme->operator_image_avatar !== false) : ?>
        <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="chat-operators mi-fs15 mr-0">
            <?php if ($theme->operator_image_url !== false) : ?>
                <img class="profile-msg-pic" src="<?php echo $theme->operator_image_url?>" alt="">
            <?php else : ?>
                <img class="profile-msg-pic" src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($theme->bot_configuration_array['operator_avatar'])?>" alt="" />
            <?php endif; ?>
        </i>
    <?php else : ?>
        <i title="<?php echo htmlspecialchars($msg['name_support'])?>" class="material-icons chat-operators mi-fs15 mr-0"><?php if (isset($react) && $react == true) : ?>&#xf10d;<?php else : ?>account_box<?php endif; ?> </i>
        <span class="op-nick-title"><?php echo htmlspecialchars($msg['name_support'])?></span>
    <?php endif; ?>
</span>