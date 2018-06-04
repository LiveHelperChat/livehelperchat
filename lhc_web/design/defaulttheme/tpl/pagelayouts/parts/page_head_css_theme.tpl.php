<?php if (isset($Result['theme']) && $Result['theme'] !== false) : ?>
    <style>
        <?php if ($Result['theme']->buble_visitor_background != '') : ?>
        div.message-row.response div.msg-body{background-color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_background)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_visitor_text_color != '') : ?>
        div.message-row.response div.msg-body{color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_text_color)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_visitor_title_color != '') : ?>
        .vis-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_title_color)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_background != '') : ?>
        div.message-admin div.msg-body{background-color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_background)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_text_color != '') : ?>
        div.message-admin div.msg-body{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_text_color)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_title_color != '') : ?>
        .op-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_title_color)?>;}
        <?php endif;?>

        .btn-bot,.btn-bot:hover,.btn-bot:focus,.btn-bot:active{
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_border']) && $Result['theme']->bot_configuration_array['bot_button_border'] != '') : ?>
            border-color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_border']);?>;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_text_color']) && $Result['theme']->bot_configuration_array['bot_button_text_color'] != '') : ?>
            color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_text_color']);?>;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_background']) && $Result['theme']->bot_configuration_array['bot_button_background'] != '') : ?>
            background-color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_background']);?>;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_border_radius']) && $Result['theme']->bot_configuration_array['bot_button_border_radius'] != '') : ?>
            border-radius: <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_border_radius']);?>px;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_padding']) && $Result['theme']->bot_configuration_array['bot_button_padding'] != '' && isset($Result['theme']->bot_configuration_array['bot_button_padding_left_right']) && $Result['theme']->bot_configuration_array['bot_button_padding_left_right'] != '') : ?>
            padding: <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_padding']);?>px <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_padding_left_right']);?>px;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_fs']) && $Result['theme']->bot_configuration_array['bot_button_fs'] != '') : ?>
            font-size: <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_fs']);?>px;
            <?php endif; ?>
        }

        <?php if (isset($Result['theme']->bot_configuration_array['bot_button_background_hover']) && $Result['theme']->bot_configuration_array['bot_button_background_hover'] != '') : ?>
        .btn-bot:hover{
            background-color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_background_hover']);?>
        }
        <?php endif; ?>

    </style>
<?php endif;?>