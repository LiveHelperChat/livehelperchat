<?php if (erLhcoreClassChat::hasAccessToRead($chat)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
<?php else : ?>
    <div class="alert alert-light my-3 bg-light" role="alert">
        <span class="material-icons">lock</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','Chat messages were hidden. You do not have permission to access department')?> - <i class="material-icons">query_builder</i><?php echo $chat->time_created_front?> - <span class="material-icons user-select-none">vpn_key</span><?php echo $chat->id?> - <i class="material-icons">home</i><b><?php echo htmlspecialchars($chat->department)?></b>
    </div>
<?php endif; ?>

<?php if ($initial == true) : ?>
    <div class="alert alert-info my-3" role="alert" style="padding:5px" id="scroll-to-chat-<?php echo $chat->id?>-<?php echo $message_start?>">
        <p class="mb-0 text-center"><b><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i> <i class="material-icons fs24">&#xE889;</i>
            <?php if (isset($chat) && isset($chat_id_messages) && is_object($chat) && $chat_id_messages == $chat->id) : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','Continue of the chat')?> - <span class="material-icons user-select-none">vpn_key</span><?php echo $chat->id?>
            <?php else : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','End of the chat')?> - <i class="material-icons">query_builder</i><?php echo $chat->time_created_front?> - <span class="material-icons user-select-none">vpn_key</span><?php echo $chat->id?> - <i class="material-icons">home</i><?php echo htmlspecialchars($chat->department)?>
            <?php endif; ?><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i></b>
        </p>
    </div>
<?php elseif (isset($chat) && isset($chat_history) && is_object($chat) && is_object($chat_history) && $chat->id != $chat_history->id ) : ?>
    <?php if ($message_start == 0) : ?>
    <div class="alert alert-info my-3" role="alert" style="padding:5px" id="scroll-to-chat-<?php echo $chat->id?>-<?php echo $message_start?>">
        <p class="mb-0 text-center"><b>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons fs24">&#xE889;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','End of the chat')?> - <i class="material-icons">query_builder</i><?php echo $chat->time_created_front?> - <span class="material-icons user-select-none">vpn_key</span><?php echo $chat->id?> - <i class="material-icons">home</i><?php echo htmlspecialchars($chat->department)?>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
            </b>
        </p>
    </div>
    <?php endif; ?>
<?php elseif (isset($chat) && is_object($chat) && $message_start == 0) : ?>
    <div class="alert alert-info my-3" role="alert" style="padding:5px" id="scroll-to-chat-<?php echo $chat->id?>-<?php echo $message_start?>">
        <p class="mb-0 text-center"><b>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons fs24">&#xE889;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','End of the chat')?> - <i class="material-icons">query_builder</i><?php echo $chat->time_created_front?> - <span class="material-icons user-select-none">vpn_key</span><?php echo $chat->id?> - <i class="material-icons">home</i><?php echo htmlspecialchars($chat->department)?>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
            </b>
        </p>
    </div>
<?php endif; ?>