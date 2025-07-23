<div class="col-6 pb-1">
    <div class="department-id" data-dep-id="<?php echo $chat->dep_id?>">
        <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Department ID')?> - <?php echo $chat->dep_id?>" class="material-icons">home</i><?php if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST) : ?><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','This is offline message')?>" class="material-icons">mail</i><?php endif?><?php echo htmlspecialchars($chat->department);?>
    </div>
</div>