<?php if (isset($Result['theme']) && $Result['theme'] !== false) : ?>
<?php if (!isset($react)) : ?>
    <style>
<?php endif;
if (isset($theme) && $theme->custom_widget_css != '') {
    $matchesImport = [];
    preg_match_all("/@import url\((.*)\);/",$theme->custom_widget_css, $matchesImport);
    foreach ($matchesImport[0] as $rule) {
        echo $rule;
        $theme->custom_widget_css = str_replace($rule,'',$theme->custom_widget_css);
    }
}
?>

        <?php if ($Result['theme']->buble_visitor_background != '') : ?>
        #messagesBlock div.message-row.response div.msg-body:not(.msg-body-media):not(.msg-body-widget):not(.msg-body-emoji)
        {background-color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_background)?>!important;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_visitor_text_color != '') : ?>
        #messagesBlock div.message-row.response div.msg-body,
        #messagesBlock div.message-row.response div.msg-body a.link
        {color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_text_color)?>!important;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_visitor_title_color != '') : ?>
        #messagesBlock div.response .vis-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_title_color)?>!important;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_background != '') : ?>
        #messagesBlock div.reactions-holder,
        #messagesBlock div.message-admin div.msg-body:not(.msg-body-media):not(.msg-body-widget):not(.msg-body-emoji){background-color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_background)?>!important;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_text_color != '') : ?>
        #messagesBlock .reaction-item,
        #messagesBlock .reaction-item:hover,
        #messagesBlock div.message-admin div.msg-body,
        #messagesBlock div.message-admin div.msg-body a.link{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_text_color)?>!important;}
        <?php endif;?>

        <?php if ($Result['theme']->buble_operator_title_color != '') : ?>
        #messagesBlock div.message-admin .op-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_title_color)?>!important;}
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

<?php if (isset($react)) : ?>

<?php if (isset($theme->bot_configuration_array['hide_visitor_profile']) && $theme->bot_configuration_array['hide_visitor_profile'] == 1) : ?>
.vis-tit {
    display: none!important;
}
<?php endif; ?>

<?php if (isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1) : ?>
.user-nick-title,
.op-nick-title
{
    display: none!important;
}
.op-tit {
    float:left!important;
    margin-top: 2px!important;
}

.vis-tit {
    float:right;
    margin-left: 3px!important;
    margin-top: 3px!important;
    position: absolute!important;
    right: 0;
}

.op-tit {
    position: absolute!important;
}

.op-tit i.material-icons,
.vis-tit i.material-icons{
    font-size: 24px!important;
}

div.message-admin div.msg-body,
div.message-admin div.meta-message{
    margin-left:29px!important;
}

<?php if (!(isset($theme->bot_configuration_array['hide_visitor_profile']) && $theme->bot_configuration_array['hide_visitor_profile'] == 1)) : ?>
div.response div.msg-body,
div.response div.meta-message{
    margin-right:29px!important;
}
.message-row.response .reactions-holder {
    right:29px!important;
}
<?php endif; ?>


@media (min-width: 1024px) {
    .profile-msg-pic {
        width: 33px!important;
    }

    div.message-admin div.msg-body,
    div.message-admin div.meta-message{
        margin-left:42px!important;
    }

    <?php if (!(isset($theme->bot_configuration_array['hide_visitor_profile']) && $theme->bot_configuration_array['hide_visitor_profile'] == 1)) : ?>
    div.response div.msg-body,
    div.response div.meta-message{
        margin-right:42px!important;
    }
    .op-tit i.material-icons,
    .vis-tit i.material-icons{
        font-size: 34px!important;
    }
    <?php endif; ?>

}
<?php endif; ?>

.header-chat {
    background-color: #<?php echo $theme->header_background;?>!important;
<?php if ($theme->header_height > 0) : ?>
    height: <?php echo $theme->header_height?>px!important;
<?php endif; ?>

<?php if ($theme->header_padding > 0) : ?>
    padding: <?php echo $theme->header_padding?>px!important;
<?php endif; ?>

}

.desktop-header,.desktop-body{
    border-color:#<?php echo $theme->widget_border_color?>!important;
    <?php if (is_numeric($theme->widget_border_width)) : ?>
    border-width: <?php echo $theme->widget_border_width?>px!important;
    <?php endif; ?>
}

<?php if ($theme->widget_position == 'full_height_right' || $theme->widget_position == 'full_height_left') : ?>
.desktop-header,.desktop-body{
    border-radius: 0!important;
}
<?php endif; ?>

<?php if (isset($theme->bot_configuration_array['header_icon_color']) && $theme->bot_configuration_array['header_icon_color'] != '') : ?>
.header-link,
.header-link:hover,
.header-link .material-icons{
    color: #<?php echo $theme->bot_configuration_array['header_icon_color']?>!important;
}
<?php endif; ?>

<?php if (
        (isset($theme->bot_configuration_array['bg_scroll_bottom']) && $theme->bot_configuration_array['bg_scroll_bottom'] != '') ||
         (isset($theme->bot_configuration_array['text_scroll_bottom']) && $theme->bot_configuration_array['text_scroll_bottom'] != '')
) : ?>
.btn-bottom-scroll > button{
    <?php if (isset($theme->bot_configuration_array['text_scroll_bottom']) && $theme->bot_configuration_array['text_scroll_bottom'] != '') : ?>
    color: #<?php echo $theme->bot_configuration_array['text_scroll_bottom']?>!important;
    <?php endif; ?>
    <?php if (isset($theme->bot_configuration_array['bg_scroll_bottom']) && $theme->bot_configuration_array['bg_scroll_bottom'] != '') : ?>
    background-color: #<?php echo $theme->bot_configuration_array['bg_scroll_bottom']?>!important;
    border-color: #<?php echo $theme->bot_configuration_array['bg_scroll_bottom']?>!important;
    <?php endif; ?>
}
<?php endif; ?>

<?php if (isset($theme->bot_configuration_array['bg_new_msg']) && $theme->bot_configuration_array['bg_new_msg'] != '') : ?>
    #scroll-to-message {
        border-bottom-color: #<?php echo $theme->bot_configuration_array['bg_new_msg']?>!important;
    }
    .new-msg-holder > .new-msg {
        background-color: #<?php echo $theme->bot_configuration_array['bg_new_msg']?>!important;
    }
<?php endif; ?>

<?php if (isset($theme->bot_configuration_array['new_msg_text_color']) && $theme->bot_configuration_array['new_msg_text_color'] != '') : ?>
.new-msg-holder > .new-msg {
    color: #<?php echo $theme->bot_configuration_array['new_msg_text_color']?>!important;
}
<?php endif; ?>

<?php echo $theme->custom_widget_css?>

<?php if (isset($popup) && $popup == true) : ?>
    <?php echo $theme->custom_popup_css?>
<?php endif; ?>

<?php endif; ?>


<?php if (!isset($react)) : ?>
    </style>
<?php endif;?>


<?php endif;?>