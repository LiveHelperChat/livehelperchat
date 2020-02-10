<div class="user-chatwidget-buttons" id="ChatSendButtonContainer">
    <a href="#" onclick="<?php if (isset($chatbox)) : ?>lhinst.addmsguserchatbox();<?php else : ?>lhinst.addmsguser(true)<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat', 'Send') ?>">
        <i class="material-icons text-muted settings">send</i>
    </a>
</div>

