<?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>
    <i class="material-icons chat-pending">chat</i><?php if (!isset($hideStatusText)) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chat');?><?php endif; ?>
<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
    <i class="material-icons chat-active">chat</i><?php if (!isset($hideStatusText)) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Active chat');?><?php endif; ?>
<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
    <i class="material-icons chat-closed">chat</i><?php if (!isset($hideStatusText)) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Closed chat');?><?php endif; ?>
<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
    <i class="material-icons chat-active">chat</i><?php if (!isset($hideStatusText)) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Chatbox chat');?><?php endif; ?>
<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
    <i class="material-icons chat-active">face</i><?php if (!isset($hideStatusText)) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Operators chat');?><?php endif; ?>
<?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?>
    <i class="material-icons chat-active">android</i><?php if (!isset($hideStatusText)) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Bot chat');?><?php endif; ?>
<?php endif;?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_multiinclude.tpl.php'));?>