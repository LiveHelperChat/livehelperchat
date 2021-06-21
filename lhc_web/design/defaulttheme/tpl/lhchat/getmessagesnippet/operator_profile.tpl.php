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