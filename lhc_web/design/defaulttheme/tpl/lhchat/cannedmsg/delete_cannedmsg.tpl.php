<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = $update_records . ' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','records will be deleted.');
$modalSize = 'lg';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/(export)/4?<?php echo $appendPrintExportURL?>" method="post" target="_blank" id="start-deletion-action" onsubmit="return false">

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Updated!') ?>
            </div>
        <?php else : ?>
            <div class="modal-body" >

                <div id="delete-progress" style="display: none"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Left to delete')?> - <span id="left-to-delete"><?php echo $update_records?></span></div>

                <?php if (!(isset($updated) && $updated == true)) : ?>
                    <button type="submit" name="XLS" id="start-button-delete" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Start deletion')?></button>
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

            function doDelete(url){
                $.postJSON(url, {'start': true}, function(data) {
                    $('#left-to-delete').html(data.left_to_delete);
                    if (data.left_to_delete > 0 && $('body').hasClass('modal-open')) {
                        doDelete(url);
                    }
                });
            }

            $('#start-deletion-action').on('submit',function() {
                $('#delete-progress').show();
                $('#start-button-delete').hide();
                doDelete($(this).attr('action'));
                return false;
            });
        })();
    </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>