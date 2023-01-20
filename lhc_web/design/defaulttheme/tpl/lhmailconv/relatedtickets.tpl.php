<?php if (!empty($mails)) : ?>
<ul class="fs14 list-unstyled mb-0" style="max-height: 108px;overflow-y: auto;">
    <?php foreach ($mails as $mail) : ?>
        <li class="text-muted <?php if (!(erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail))) : ?>opacity-50<?php endif;?>">
            <input type="checkbox" class="me-1" <?php if (!(erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail))) : ?>disabled="disabled" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','You do not have permission to close conversation')?>" <?php else : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Check e-email for close on ticket close')?>" <?php endif; ?> onchange="lhinst.setAreaAttrByCheckbox(<?php echo $chat->id?>,'closemail');" name="closemail-<?php echo $chat->id?>" value="<?php echo $mail->id?>" <?php if (erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail) && $mail->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) : ?>checked="checked"<?php endif; ?>  />
            <span class="material-icons user-select-none">mail</span><span><?php echo $mail->id?></span><span class="ms-1 me-1 badge <?php if ($mail->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>bg-warning<?php else : ?>bg-success<?php endif; ?>">
                <?php if ($mail->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','pending')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','active')?><?php endif; ?>
            </span>
            <?php if (erLhcoreClassChat::hasAccessToRead($mail) && erLhcoreClassChat::hasAccessToWrite($mail)) : ?>
            <a onclick="lhc.previewMail(<?php echo $mail->id?>);" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Preview')?>" class="material-icons text-muted">info_outline</a>
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
<?php else : ?>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','No related mail tickets were found!')?>
<?php endif; ?>
