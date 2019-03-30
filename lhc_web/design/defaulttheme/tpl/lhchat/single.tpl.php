<div id="tabs" class="chat-tabs-container h-100" id="chatdashboard" <?php if (is_numeric($chat_id)) : ?>ng-init="lhc.startChatDashboard(<?php echo (int)$chat_id?>,{'remember' : true})"<?php endif; ?>>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list.tpl.php')); ?>
</div>

