<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose mailing list to import from');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
    <form action="<?php echo $action_url?>" ng-non-bindable method="post" target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>
        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <ul>
                    <li><?php echo $statistic['imported']?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','were assigned');?></li>
                    <li><?php echo $statistic['already_exists']?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','already existed');?></li>
                    <li><?php echo $statistic['skipped']?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','were skipped');?></li>
                    <li><?php echo $statistic['unassigned']?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','were removed from recipient list');?></li>
                </ul>
            </div>
            <script>
                $('#list-update-import').removeClass('hide');
            </script>
        <?php else : ?>
            <div class="modal-body">
                <?php
                    $params = array (
                        'input_name'     => 'ml[]',
                        'display_name'   => 'name',
                        'css_class'      => 'form-control',
                        'multiple'       => true,
                        'wrap_prepend'   => '<div class="col-4">',
                        'wrap_append'    => '</div>',
                        'selected_id'    => [],
                        'list_function'  => 'erLhcoreClassModelMailconvMailingList::getList',
                        'list_function_params'  => array('sort' => 'name ASC, id ASC', 'limit' => false)
                    );
                    echo erLhcoreClassRenderHelper::renderCheckbox( $params );
                ?>
            </div>
            <input type="hidden" id="id_export_action" name="export_action" value="doExport">
        <?php endif; ?>
        <div class="modal-footer">
            <div class="btn-group">
                <?php if (!(isset($updated) && $updated == true)) : ?>
                    <button type="submit" name="AssignAction" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Assign from selected list')?></button>
                    <button type="submit" name="UnassignedAction" onclick="$('#id_export_action').val('unassign')" class="btn btn-warning btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Un-assign from selected list')?></button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
            </div>
        </div>
    </form>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>