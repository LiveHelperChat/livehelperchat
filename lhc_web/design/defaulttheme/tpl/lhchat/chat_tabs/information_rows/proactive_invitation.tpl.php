<?php if ($chat->invitation_id > 0) : ?>
    <div class="col-6 pb-1">
        <div>
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Proactive invitation')?> - <?php echo $chat->invitation_id?>" class="material-icons">drafts</i><?php echo htmlspecialchars(erLhAbstractModelProactiveChatInvitation::fetch($chat->invitation_id));?>
        </div>
    </div>
<?php endif; ?>