<?php $hasSelectedRelated = false; if (!empty($mails)) : ?>
    <ul class="fs14 list-unstyled mb-0" style="max-height: 108px;overflow-y: auto;">
        <?php foreach ($mails as $mail) : ?>
            <li class="text-muted <?php if (!(erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail))) : ?>opacity-50<?php endif;?>">
                <input class="me-1" type="checkbox" <?php if (!(erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail))) : ?>disabled="disabled" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','You do not have permission to close conversation')?>" <?php else : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Check e-email for close on ticket close')?>"<?php endif;?> onchange="lhinst.setAreaAttrByCheckbox(<?php echo $chat->id?>,'closemailconfirm');$('#related-actions-<?php echo $chat->id?>').text($('#related-actions-<?php echo $chat->id?>').attr(document.querySelectorAll('input[name=closemailconfirm-<?php echo $chat->id?>]:checked').length > 0 ? 'data-action-close' : 'data-action-continue'));" name="closemailconfirm-<?php echo $chat->id?>" value="<?php echo $mail->id?>" <?php if (((isset($related_actions['closemail']) && is_array($related_actions['closemail']) && in_array($mail->id,$related_actions['closemail'])) || (!isset($related_actions['closemail']) && $mail->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY)) && erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail)) : $hasSelectedRelated = true;?> checked="checked" <?php endif; ?>  />
                <span class="material-icons user-select-none">mail</span><span><?php echo $mail->id?></span><span class="ms-1 me-1 badge <?php if ($mail->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>bg-warning<?php else : ?>bg-success<?php endif; ?>">
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
                <?php endif; ?><?php echo htmlspecialchars($mail->subject)?>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        lhinst.setAreaAttrByCheckbox(<?php echo $chat->id?>,'closemailconfirm');
    </script>
<?php else : ?>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','No related mail tickets were found!')?>
<?php endif; ?>