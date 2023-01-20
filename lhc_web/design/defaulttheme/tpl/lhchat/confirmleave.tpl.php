<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Are you sure you want to close this chat?'); $hideModalClose = true; ?>

<div class="modal-dialog modal-<?php isset($modalSize) ? print $modalSize : print 'lg'?> modal-confirm-leave mx-4">
    <div class="modal-content">
    <div class="modal-header<?php (isset($modalHeaderClass)) ? print ' '.$modalHeaderClass : ''?>">
        <?php if (!isset($hideModalClose)) : ?><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php endif;?>
        <h4 class="modal-title" id="myModalLabel"><?php isset($modalHeaderTitle) ? print $modalHeaderTitle : ''?></h4>
    </div>

<div class="modal-body<?php (isset($modalBodyClass)) ? print ' '.$modalBodyClass : ''?>">
    <div class="mb-0" style="padding:0px 0 10px 0;">
        <div class="row">
            <div class="col-5">
                <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Yes')?>" class="btn btn-primary btn-sm w-100" data-action="confirmClose">
            </div>
            <div class="col-2"></div>
            <div class="col-5">
                <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?>" class="btn btn-link text-muted btn-sm w-100" data-action="cancelClose">
            </div>
        </div>
    </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>