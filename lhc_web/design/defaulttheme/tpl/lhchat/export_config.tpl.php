<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export XLS/CSV');
$modalSize = 'md';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

<form action="<?php echo htmlspecialchars($action_url)?>/(export)/1?<?php echo htmlspecialchars(isset($query_url) ? $query_url : '')?><?php echo !empty($appendPrintExportURL) ? '&amp;' . $appendPrintExportURL : ''?>" method="post" target="_blank">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="modal-body">
        <ul class="nav nav-tabs" id="exportConfigTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="export-options-tab" data-bs-toggle="tab" data-bs-target="#export-options" type="button" role="tab" aria-controls="export-options" aria-selected="true">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export options')?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="export-system-tab" data-bs-toggle="tab" data-bs-target="#export-system" type="button" role="tab" aria-controls="export-system" aria-selected="false">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','ChatML')?>
                </button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="exportConfigTabsContent">
            <div class="tab-pane fade show active" id="export-options" role="tabpanel" aria-labelledby="export-options-tab">
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

            <div class="tab-pane fade" id="export-system" role="tabpanel" aria-labelledby="export-system-tab">
                <div class="form-group">
                    <label for="system_prompt"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','System prompt (optional)')?></label>
                    <textarea id="system_prompt" name="system_prompt" class="form-control form-control-sm" rows="7" placeholder="<?php echo htmlspecialchars(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','This will be added as a system message for ChatML export only.'))?>"></textarea>
                </div>

                <div class="form-group mt-2">
                    <label for="last_n_messages"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include first n messages')?></label>
                    <input type="number" min="1" step="1" id="last_n_messages" name="last_n_messages" class="form-control form-control-sm" value="15">
                </div>

                <div class="form-group mt-2">
                    <label><input type="checkbox" name="exclude_operator_messages" value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Exclude operator messages')?></label>
                </div>

                <div class="form-group mt-2">
                    <label><input type="checkbox" name="only_with_tool_calls" value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export only chats with tool calls')?></label>
                </div>

                <small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Leave empty to export without a system prompt.')?></small>
            </div>
        </div>

    </div>
    <input type="hidden" name="export_action" value="doExport">
    <div class="modal-footer">
        <button type="submit" name="ChatML" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export ChatML')?></button>
        <button type="submit" name="XLS" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export XLS')?></button>
        <button type="submit" name="CSV" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export CSV')?></button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
    </div>
</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>