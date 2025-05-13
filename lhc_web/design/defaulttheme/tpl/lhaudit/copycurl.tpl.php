<?php
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Copy as CURL');
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div class="modal-body">
        <textarea class="form-control form-control-sm fs12" rows="20" id="curl-command-text"><?php echo htmlspecialchars($command);?></textarea>
    </div>

    <div class="modal-footer ps-0 pe-0 ms-0 me-0">
        <div class="row w-100 ps-0 pe-0 ms-0 me-0">
            <div class="col"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button></div>
            <div class="col ps-0 ms-0 me-0"><button type="button" data-bs-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Copied')?>" onclick="lhinst.copyContent($(this))" data-copy-id="curl-command-text" class="btn btn-success float-end"><span class="material-icons">content_copy</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Copy')?></button></div>
        </div>
    </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>