<div class="container-fluid overflow-auto fade-in p-3 pb-4 {dev_type}">
    <div class="p-2" id="start-chat-btn" style="cursor: pointer">
        <div class="shadow rounded bg-white nh-background pb-1">
            <button type="button" id="close-need-help-btn" class="close position-absolute" style="right:34px;top:28px;z-index:2" aria-label="Close">
                <span class="px-1" aria-hidden="true">×</span>
            </button>
            <div class="operator-info p-2 d-flex border-bottom">
                <div class="align-self-center op-photo">
                    <?php if ($user->has_photo_avatar) : ?>
                        <?php if ($user->has_photo) : ?>
                            <img width="20" height="20" src="<?php echo $user->photo_path?>" alt="<?php echo htmlspecialchars($user->name_support)?>" />
                        <?php else : ?>
                            <img width="20" height="20" src="<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($user->avatar)?>" alt="<?php echo htmlspecialchars($user->name_support)?>" />
                        <?php endif; ?>
                    <?php else : ?>
                        <i class="icon-assistant material-icons mr-0"><?php if (isset($react) && $react === true) : ?>&#xf10d;<?php else : ?>account_box<?php endif; ?></i>
                    <?php endif;?>
                </div>
                <div class="p-1 pl-2">
                    <span class="font-weight-bold op-name-widget">
                        <?php if (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['custom_op_name']) && $theme->bot_configuration_array['custom_op_name'] != '') : ?>
                            <?php echo htmlspecialchars(str_replace(['{nick}', '{name}', '{surname}'], [$user->name_support, $user->name, $user->surname], $theme->bot_configuration_array['custom_op_name']));?>
                        <?php else : ?>
                            <?php echo htmlspecialchars($user->name_support)?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
           <div class="bottom-message px-1 position-relative" id="messages-scroll" style="max-height:91px;font-size:14px">
                {msg_body}
            </div>
        </div>
    </div>
</div>