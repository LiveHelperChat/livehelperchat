<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save search');
$modalSize = 'md';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/(export)/2?<?php echo $appendPrintExportURL?>" method="post" target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>
        
        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Search was saved') ?>. <a href="<?php echo erLhcoreClassDesign::baseurl('views/home')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Go to my views')?></a>
            </div>
        <?php else : ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name')?></label>
                            <input required maxlength="100" class="form-control" type="text" ng-non-bindable name="name" value="<?php echo htmlspecialchars($item->name)?>" />
                        </div>
                    </div>
                    <div class="col-6">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include records from the past')?></label>
                        <input type="number" required min="30" class="form-control" placeholder="days" name="days" value="<?php echo htmlspecialchars($item->days)?>" />
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','The higher number the higher in the views list it will appear')?></label>
                            <input required maxlength="100" class="form-control" type="text" ng-non-bindable name="position" value="<?php echo htmlspecialchars($item->position)?>" />
                        </div>
                    </div>
                    <div class="col-12">
                        <label><input type="checkbox" name="passive" value="on" <?php if ($item->passive == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Passive, number of matching records will not be updated in the background.')?></label>
                    </div>

                </div>
            </div>
            <input type="hidden" name="export_action" value="doExport">
        <?php endif; ?>

        <div class="modal-footer">
            <?php if (!(isset($updated) && $updated == true)) : ?>
            <button type="submit" name="XLS" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save')?></button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
        </div>

    </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>