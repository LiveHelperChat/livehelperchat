
<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Details send');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div class="modal-body" ng-non-bindable>
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send log');?></label>
        <textarea class="form-control form-control-sm"><?php echo htmlspecialchars($item->log)?></textarea>
    </div>

    <div class="modal-footer">
        <div class="btn-group" role="group" aria-label="...">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>
    </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>