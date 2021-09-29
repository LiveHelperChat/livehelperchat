<?php ($chat_variables_array = $chat->chat_variables_array); if (!empty($chat_variables_array) && isset($chat_variables_array['iwh_id'])) : ?>
    <?php if (($incomingWebhook = erLhcoreClassModelChatIncomingWebhook::fetch($chat_variables_array['iwh_id'])) instanceof erLhcoreClassModelChatIncomingWebhook) : ?>
            <div class="col-6 pb-1">
                <span class="material-icons">extension</span>
                <?php echo htmlspecialchars($incomingWebhook->name)?>
                <?php if (isset($chat_variables_array['iwh_field']) && !empty($chat_variables_array['iwh_field'])) : ?>
                &nbsp;|&nbsp;<?php echo htmlspecialchars($chat_variables_array['iwh_field'])?>
                <?php endif; ?>
            </div>
    <?php endif; ?>
<?php endif; ?>
