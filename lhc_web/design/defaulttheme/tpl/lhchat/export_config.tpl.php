<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export XLS/CSV');
$modalSize = 'md';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

<form action="<?php echo htmlspecialchars($action_url)?>/(export)/1?<?php echo $appendPrintExportURL?>" method="post" target="_blank">
    <div class="modal-body">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><input type="checkbox" name="exportOptions[]" value="2"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include content')?></label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><input type="checkbox" name="exportOptions[]" value="3"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include survey')?></label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><input type="checkbox" name="exportOptions[]" value="4"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include messages statistic')?></label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><input type="checkbox" name="exportOptions[]" value="5"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include subject')?></label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><input type="checkbox" name="exportOptions[]" value="6"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include additional chat variables as columns')?></label>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="export_action" value="doExport">
    <div class="modal-footer">
        <button type="submit" name="XLS" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export XLS')?></button>
        <button type="submit" name="CSV" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export CSV')?></button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
    </div>
</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>