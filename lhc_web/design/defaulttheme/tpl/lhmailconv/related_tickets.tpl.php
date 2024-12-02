<?php $hasSelectedRelated = false; if (!empty($mails)) :
    $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options_general');
    $data = (array)$mcOptions->data;
    ?>
    <ul class="fs14 list-unstyled mb-0" style="max-height: 108px;overflow-y: auto;">
        <?php foreach ($mails as $mail) :
            $excludeMailbox = false;
            $mailboxMail = '';
            if (!empty($data['exclude_mailbox'])) {
                foreach (explode("\n",$data['exclude_mailbox']) as $excludeMailboxItem) {
                    if (preg_match($excludeMailboxItem, (string)$mail->mailbox)) {
                        $mailboxMail = (string)$mail->mailbox;
                        $excludeMailbox = true;
                    }
                }
            } ?>
            <li class="text-muted <?php if ($excludeMailbox === true || !(erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail))) : ?>opacity-50<?php endif;?>">
                <input class="me-1" type="checkbox" <?php if ($excludeMailbox === true || !(erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail))) : ?>disabled="disabled" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','You do not have permission to close conversation')?>" <?php else : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Check e-email for close on ticket close')?>"<?php endif;?> onchange="lhinst.setAreaAttrByCheckbox(<?php echo $chat->id?>,'closemailconfirm');$('#related-actions-<?php echo $chat->id?>').text($('#related-actions-<?php echo $chat->id?>').attr(document.querySelectorAll('input[name=closemailconfirm-<?php echo $chat->id?>]:checked').length > 0 ? 'data-action-close' : 'data-action-continue'));" name="closemailconfirm-<?php echo $chat->id?>" value="<?php echo $mail->id?>" <?php if (((isset($related_actions['closemail']) && is_array($related_actions['closemail']) && in_array($mail->id,$related_actions['closemail'])) || (!isset($related_actions['closemail']) && $mail->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY)) && $excludeMailbox === false && erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail)) : $hasSelectedRelated = true;?> checked="checked" <?php endif; ?>  />
                <span title="<?php echo htmlspecialchars((string)$mail->mailbox);?>" class="material-icons user-select-none">mail</span><span><?php echo $mail->id?></span><span class="ms-1 me-1 badge <?php if ($mail->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>bg-warning<?php else : ?>bg-success<?php endif; ?>">
                <?php if ($mail->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','pending')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','active')?><?php endif; ?>
            </span>
                <?php if (erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail)) : ?>
                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Preview')?>" class="action-image text-muted material-icons" data-title="<?php echo htmlspecialchars($mail->subject)?>" onclick="lhinst.startMailNewWindow(<?php echo $mail->id?>,$(this).attr('data-title'))" >open_in_new</a>
                <?php endif;?>
                <i title="<?php echo htmlspecialchars($mail->department)?>" class="material-icons">home</i>
                <?php if ($mail->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) : ?>
                    <span class="material-icons text-primary">attach_file</span><span class="material-icons text-primary">image</span>
                <?php elseif ($mail->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) : ?>
                    <span class="material-icons text-primary">attach_file</span>
                <?php elseif ($mail->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) : ?>
                    <span class="material-icons text-primary">image</span>
                <?php endif; ?><?php echo erLhcoreClassDesign::shrt($mail->subject,60,'...',30,ENT_QUOTES)?><?php if ($excludeMailbox === true) : ?> <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mail excluded from auto closing!')?>" class="material-icons text-danger">block</span>[<?php echo htmlspecialchars($mailboxMail)?>] <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','excluded from auto closing!')?><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        lhinst.setAreaAttrByCheckbox(<?php echo $chat->id?>,'closemailconfirm');
    </script>
<?php else : ?>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','No related mail tickets were found!')?>
<?php endif; ?>