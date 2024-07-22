<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save search');
$modalSize = 'md';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/(export)/2?<?php echo htmlspecialchars(isset($query_url) ? $query_url : '')?><?php echo !empty($appendPrintExportURL) ? '&amp;' . $appendPrintExportURL : ''?>" method="post" ng-non-bindable target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

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
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name')?></label>
                            <input required maxlength="100" class="form-control form-control-sm" type="text" ng-non-bindable name="name" value="<?php echo htmlspecialchars($item->name)?>" />
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range filter')?></label>
                            <select name="timefrom_type" class="form-control form-control-sm">
                                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','My defined date range')?></option>
                                <option value="today" <?php if ($input->timefrom_type == 'today') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Today')?></option>
                                <option value="range-yesterday" <?php if ($input->timefrom_type == 'range-yesterday') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Yesterday')?></option>
                                <option value="range-last2days" <?php if ($input->timefrom_type == 'range-last2days') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last 2 days')?></option>
                                <option value="range-last7days" <?php if ($input->timefrom_type == 'range-last7days') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last 7 days')?></option>
                                <option value="range-last15days" <?php if ($input->timefrom_type == 'range-last15days') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last 15 days')?></option>
                                <option value="range-last30days" <?php if ($input->timefrom_type == 'range-last30days') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last 30 days')?></option>
                                <option value="range-thisweek" <?php if ($input->timefrom_type == 'range-thisweek') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','This week')?></option>
                                <option value="range-thismonth" <?php if ($input->timefrom_type == 'range-thismonth') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','This month')?></option>
                                <option value="range-previousweek" <?php if ($input->timefrom_type == 'range-previousweek') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous week')?></option>
                                <option value="range-previousmonth" <?php if ($input->timefrom_type == 'range-previousmonth') : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous month')?></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include records from the past')?></label>
                            <input type="number" required min="30" class="form-control form-control-sm" placeholder="days" name="days" value="<?php echo htmlspecialchars($item->days)?>" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','The higher number the higher in the views list it will appear')?></label>
                            <input required maxlength="100" class="form-control form-control-sm" type="text" ng-non-bindable name="position" value="<?php echo htmlspecialchars($item->position)?>" />
                        </div>
                    </div>
                    <div class="col-12">
                        <label><input type="checkbox" name="passive" value="on" <?php if ($item->passive == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Passive, number of matching records will not be updated in the background.')?></label>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Description of your view')?></label>
                            <textarea name="description" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Put description for your own purposes.')?>" class="form-control form-control-sm"></textarea>
                        </div>
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

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>