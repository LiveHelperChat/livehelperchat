   <div class="chart-settings">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/chart_user_settings/option_sound.tpl.php'));?>

        <?php if (isset($chat)) : ?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chart_user_settings/notifications.tpl.php'));?>

            <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chart_user_settings/option_print.tpl.php'));?>
            <?php endif;?>

            <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chart_user_settings/option_transcript.tpl.php'));?>
            <?php endif;?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chart_user_settings/user_file_upload.tpl.php'));?>

        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/chart_user_settings/option_last_multiinclude.tpl.php'));?>
   </div>