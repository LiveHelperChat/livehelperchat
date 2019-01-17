<?php if (isset($Result['theme']) && $Result['theme'] !== false) : ?>
    <style>
        <?php if ($Result['theme']->buble_visitor_background != '') : ?>
        #messagesBlock div.message-row.response div.msg-body{background-color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_background)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_visitor_text_color != '') : ?>
        #messagesBlock div.message-row.response div.msg-body,
        #messagesBlock div.message-row.response div.msg-body a.link
        {color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_text_color)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_visitor_title_color != '') : ?>
        #messagesBlock div.response .vis-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_title_color)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_background != '') : ?>
        #messagesBlock div.message-admin div.msg-body{background-color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_background)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_text_color != '') : ?>
        #messagesBlock div.message-admin div.msg-body,
        #messagesBlock div.message-admin div.msg-body a.link{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_text_color)?>;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_title_color != '') : ?>
        #messagesBlock div.message-admin .op-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_title_color)?>;}
        <?php endif;?>

        .btn-bot,.btn-bot:hover,.btn-bot:focus,.btn-bot:active{
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_border']) && $Result['theme']->bot_configuration_array['bot_button_border'] != '') : ?>
            border-color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_border']);?>!important;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_text_color']) && $Result['theme']->bot_configuration_array['bot_button_text_color'] != '') : ?>
            color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_text_color']);?>!important;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_background']) && $Result['theme']->bot_configuration_array['bot_button_background'] != '') : ?>
            background-color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_background']);?>!important;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_border_radius']) && $Result['theme']->bot_configuration_array['bot_button_border_radius'] != '') : ?>
            border-radius: <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_border_radius']);?>px!important;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_padding']) && $Result['theme']->bot_configuration_array['bot_button_padding'] != '' && isset($Result['theme']->bot_configuration_array['bot_button_padding_left_right']) && $Result['theme']->bot_configuration_array['bot_button_padding_left_right'] != '') : ?>
            padding: <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_padding']);?>px <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_padding_left_right']);?>px!important;
            <?php endif; ?>
            <?php if (isset($Result['theme']->bot_configuration_array['bot_button_fs']) && $Result['theme']->bot_configuration_array['bot_button_fs'] != '') : ?>
            font-size: <?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_fs']);?>px!important;
            <?php endif; ?>
        }

        <?php if (isset($Result['theme']->bot_configuration_array['bot_button_background_hover']) && $Result['theme']->bot_configuration_array['bot_button_background_hover'] != '') : ?>
        .btn-bot:hover,.btn-bot:active,.btn-bot:focus{
            background-color: #<?php echo htmlspecialchars($Result['theme']->bot_configuration_array['bot_button_background_hover']);?>!important;
        }
        <?php endif; ?>

    </style>
<?php endif;?>