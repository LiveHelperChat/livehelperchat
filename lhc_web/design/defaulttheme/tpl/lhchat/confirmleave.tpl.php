<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Are you sure you want to close this chat?'); $hideModalClose = true; ?>

<div class="modal-dialog modal-<?php isset($modalSize) ? print $modalSize : print 'lg'?>">
    <div class="modal-content">
    <div class="modal-header<?php (isset($modalHeaderClass)) ? print ' '.$modalHeaderClass : ''?>">
        <?php if (!isset($hideModalClose)) : ?><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php endif;?>
        <h4 class="modal-title" id="myModalLabel"><?php isset($modalHeaderTitle) ? print $modalHeaderTitle : ''?></h4>
    </div>

<div class="modal-body<?php (isset($modalBodyClass)) ? print ' '.$modalBodyClass : ''?>">
    <div class="mb-0" style="padding:0px 0 10px 0;">
        <div class="btn-group d-flex" role="group" aria-label="...">
            <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Yes')?>" class="btn btn-success btn-sm w-100" data-action="confirmClose">
            <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','No')?>" class="btn btn-warning btn-sm w-100" data-action="cancelClose">
        </div>
    </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>