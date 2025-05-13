<?php

$modalHeaderTitle = '';
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalSize = 'md';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';

?>

<div class="modal-dialog modal-dialog-scrollable modal-<?php isset($modalSize) ? print $modalSize : print 'lg'?>">
    <div class="modal-content">
    <div ng-non-bindable class="modal-header<?php (isset($modalHeaderClass)) ? print ' '.$modalHeaderClass : ''?>">
        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">help</span></h4>
        <?php if (!isset($hideModalClose)) : ?><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button><?php endif;?>
    </div>
<div class="modal-body<?php (isset($modalBodyClass)) ? print ' '.$modalBodyClass : ''?>">
    <div class="modal-body">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Are you sure?')?></h5>
    </div>
    <div class="modal-footer ps-0 pe-0 ms-0 me-0">
        <div class="row w-100 ps-0 pe-0 ms-0 me-0">
            <div class="col"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?></button></div>
            <div class="col ps-0 ms-0 me-0"><button type="button" id="confirm-button-action" class="btn btn-danger float-end"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Proceed')?></button></div>
        </div>
    </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>