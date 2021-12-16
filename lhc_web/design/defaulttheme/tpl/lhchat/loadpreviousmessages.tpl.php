<?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
<?php if ($initial == true) : ?>
    <div class="alert alert-info" role="alert" style="margin:10px 10px 30px 10px;padding:5px" id="scroll-to-chat-<?php echo $chat->id?>-<?php echo $message_start?>">
        <p class="mb-0 text-center"><b><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i> <i class="material-icons fs24">&#xE889;</i>
            <?php if (isset($chat) && isset($chat_history) && is_object($chat) && is_object($chat_history) && $chat->id == $chat_history->id && $chat_id_original == $chat->id) : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','Continue of the chat')?> - <?php echo $chat->id?>
            <?php else : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','End of the chat')?> - <?php echo $chat->id?>
            <?php endif; ?><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i><i class="material-icons">&#xE316;</i></b>
        </p>
    </div>
<?php elseif (isset($chat) && isset($chat_history) && is_object($chat) && is_object($chat_history) && $chat->id != $chat_history->id ) : ?>
    <?php if ($message_start == 0) : ?>
    <div class="alert alert-info" role="alert" style="margin:10px 10px 30px 10px;padding:5px" id="scroll-to-chat-<?php echo $chat->id?>-<?php echo $message_start?>">
        <p class="mb-0 text-center"><b>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons fs24">&#xE889;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','End of the chat')?>  - <?php echo $chat->id?>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
            </b>
        </p>
    </div>
    <?php endif; ?>
<?php elseif (isset($chat) && is_object($chat) && $message_start == 0) : ?>
    <div class="alert alert-info" role="alert" style="margin:10px 10px 30px 10px;padding:5px" id="scroll-to-chat-<?php echo $chat->id?>-<?php echo $message_start?>">
        <p class="mb-0 text-center"><b>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons fs24">&#xE889;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/loadprevious','End of the chat')?> -  <?php echo $chat->id?>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
                <i class="material-icons">&#xE316;</i>
            </b>
        </p>
    </div>
<?php endif; ?>