<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = $update_records . ' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','records will be deleted and archived.');
$modalSize = 'lg';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/(export)/5?<?php echo $appendPrintExportURL?>" method="post" target="_blank" id="start-deletion-action" onsubmit="return false">

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Updated!') ?>
            </div>
        <?php else : ?>
            <div class="modal-body" >

                <div id="delete-progress" style="display: none"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Left to delete and archive')?> - <span id="left-to-delete"><?php echo $update_records?></span></div>

                <?php if (!(isset($updated) && $updated == true)) : ?>

                    <div class="form-group">
                        <label><input type="checkbox" id="id_delete_policy" name="delete_policy" checked="checked" value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete policy is active.')?></label>
                        <div class="text-muted"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','If checked we will process message per mailbox settings. If not checked we will ignore mailbox setting and do not touch mail messages on imap.')?></small></div>
                    </div>

                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose an archive')?></label>
                        <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
                                'input_name' => 'archive_id',
                                'selected_id' => '',
                                'display_name' => 'name',
                                'css_class' => 'form-control form-control-sm',
                                'attr_id' => 'id',
                                'list_function' => '\LiveHelperChat\Models\mailConv\Archive\Range::getList',
                                'list_function_params' => array('filter' => array('type' => 1))
                            ));
                        ?>
                    </div>
                    <?php if (\LiveHelperChat\Models\mailConv\Archive\Range::getCount(array('filter' => array('type' => 1))) > 0) : ?>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" name="XLSSchedule" id="start-schedule-delete" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Start deletion and archiving in background')?></button>
                        <button type="submit" name="XLS" id="start-button-delete" class="btn btn-secondary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Start deletion and archiving')?></button>
                    </div>
                    <?php else : ?>
                        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Please create a backup archive first!')?></p>
                    <?php endif; ?>
                <?php endif; ?>

            </div>

            <input type="hidden" name="export_action" value="doExport">
        <?php endif; ?>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>

    </form>

    <script>
        (function(){
            var total_records = <?php echo (int)$update_records;?>;

            function doDelete(url){
                $.postJSON(url, {'start': true, 'delete_policy': $('#id_delete_policy').is(':checked') }, function(data) {
                    total_records = total_records - data.left_to_delete;
                    $('#left-to-delete').html(total_records);
                    if (data.left_to_delete > 0 && $('body').hasClass('modal-open')) {
                        doDelete(url);
                    }
                });
            }

            $('#start-deletion-action').on('submit',function() {
                $('#delete-progress').show();
                $('#start-button-delete,#start-schedule-delete').hide();
                doDelete($(this).attr('action') + '&archive_id=' + document.getElementById('id_archive_id').value);
                return false;
            });

            $('#start-schedule-delete').on('click',function() {
                $.postJSON($('#start-deletion-action').attr('action')+ '&archive_id=' + document.getElementById('id_archive_id').value, {'schedule': true, 'delete_policy': $('#id_delete_policy').is(':checked')}, function(data) {
                    $('#delete-progress').show();
                    $('#start-button-delete,#start-schedule-delete').hide();
                    $('#left-to-delete').html(data.result);
                });
            });

        })();
    </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>