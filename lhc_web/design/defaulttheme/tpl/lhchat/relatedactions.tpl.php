<div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 pl-2 pr-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Related mail tickets')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body mx550">
            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/related_tickets.tpl.php'));?>
        </div>
        <div class="modal-footer">
            <p class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/relateditems','Only mails without an attachments are selected by default.')?></p>&nbsp;<button id="related-actions-<?php echo $chat->id?>" type="button" data-action-continue="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Continue')?>" data-action-close="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close selected')?>" class="btn btn-primary" onclick="lhinst.closeActiveChatDialog(<?php echo $chat->id?>,$('#tabs'),true, true)">
                <?php if (isset($hasSelectedRelated) && $hasSelectedRelated === true) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close selected')?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Continue')?>
                <?php endif; ?>
            </button>&nbsp;<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?></button>
        </div>
    </div>
</div>
