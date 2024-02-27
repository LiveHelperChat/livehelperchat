<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions').', '. $update_records . ' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','records will be updated.');
$modalSize = 'lg';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/(export)/3?<?php echo $appendPrintExportURL?>" method="post" target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Updated!') ?>
            </div>
            <script>
                setTimeout(function (){
                    document.location.reload();
                },2000);
            </script>
        <?php else : ?>
            <div class="modal-body">
                    <div role="tabpanel" class="tab-pane pt-2" id="changeowner">
                        <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Change filtered e-mails owner to selected operator')?></h6>
                        <input class="form-control mb-2 form-control-sm" onkeyup="searchUserTransfer()" id="search-changeowner" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search for a user.  First 50 users are shown.')?>" />
                        <div class="form-group" id="search-changeowner-result">
                            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                                'input_name'     => 'new_user_id',
                                'selected_id'    => 0,
                                'css_class'      => 'form-control form-control-sm',
                                'display_name'   => function($item){return $item->name_official . ($item->chat_nickname != '' ? ' | '.$item->chat_nickname : '');},
                                'size' => 10,
                                'list_function'  => 'erLhcoreClassModelUser::getUserList',
                                'list_function_params'  => array('limit' => 50, 'filter' => array('disabled' => 0))
                            )); ?>
                        </div>
                    </div>
            </div>

            <input type="hidden" name="export_action" value="doExport">
        <?php endif; ?>

        <div class="modal-footer">
            <?php if (!(isset($updated) && $updated == true)) : ?>
                <button type="submit" name="XLS" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save')?></button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>

    </form>

    <script>
        function searchUserTransfer() {
            var value = $('#search-changeowner').val();
            $.getJSON(WWW_DIR_JAVASCRIPT+ 'chat/searchprovider/users/?exclude_disabled=1&q='+escape(value), function(result){
                var resultHTML = '';
                result.items.forEach(function(item){
                    resultHTML += "<option value=\""+item.id+"\">" + $("<div>").text(item.name + (item.nick != "" ? " | " + item.nick : '')).html() + "</option>";
                });
                $('#id_new_user_id').html(resultHTML);
            });
        }
    </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>