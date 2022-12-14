<?php ($chat_variables_array = $chat->chat_variables_array); if (
        (!empty($chat_variables_array) && isset($chat_variables_array['iwh_id']) && $iwh_id = $chat_variables_array['iwh_id']) ||
        ($chat->iwh_id > 0 && $iwh_id = $chat->iwh_id)
) : ?>
    <?php if (($incomingWebhook = erLhcoreClassModelChatIncomingWebhook::fetch($iwh_id)) instanceof erLhcoreClassModelChatIncomingWebhook) : $customExternalRendered = false;?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/external_chat_render_multiinclude.tpl.php'));?>

            <?php if ($customExternalRendered == false) : ?>
            <div class="col-6 pb-1">
                <span class="material-icons" <?php if ($incomingWebhook->icon_color != '') : ?>style="color: <?php echo htmlspecialchars($incomingWebhook->icon_color)?>"<?php endif;?> ><?php if ($incomingWebhook->icon != '') : ?><?php echo htmlspecialchars($incomingWebhook->icon)?><?php else : ?>extension<?php endif; ?></span>
                <?php echo htmlspecialchars($incomingWebhook->name)?>
                <?php if (isset($chat_variables_array['iwh_field']) && !empty($chat_variables_array['iwh_field'])) : ?>
                &nbsp;|&nbsp;<?php echo htmlspecialchars($chat_variables_array['iwh_field'])?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

    <?php endif; ?>
<?php endif; ?>
