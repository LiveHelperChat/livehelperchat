<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save report');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <form action="<?php echo htmlspecialchars($action_url)?>/(export)/1" method="post" ng-non-bindable target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Report was saved')?>.
                <script>
                    setTimeout(function(){
                        document.location = '<?php echo erLhcoreClassDesign::baseurl('statistic/loadreport')?>/<?php echo $item->id?>';
                    },1000);
                </script>
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
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Description of your report')?></label>
                            <textarea name="description" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Put description for your own purposes.')?>" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                    <div class="col-12" id="report-presets">
                        <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date filter templates')?></h6>
                        <span data-data_type="ndays" data-days="1" data-days_end="1" data-hour="23" data-minute="59" data-seconds="59" class="badge-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous Day')?></span>
                        <span data-data_type="lweek" data-days="1" data-days_end="1" data-hour="23" data-minute="59" data-seconds="59" class="badge-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous Week')?></span>
                        <span data-data_type="lmonth" data-days="1" data-days_end="1" data-hour="23" data-minute="59" data-seconds="59" class="badge-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous Month')?></span>
                        <span data-data_type="ndays" data-days="0" data-days_end="0" data-hour="" data-minute="" data-seconds="" class="badge-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Current Day')?></span>
                        <span data-data_type="lweek" data-days="0" data-days_end="0" data-hour="" data-minute="" data-seconds="" class="badge-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Current Week')?></span>
                        <span data-data_type="lmonth" data-days="0" data-days_end="0" data-hour="" data-minute="" data-seconds="" class="badge-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Current month')?></span>
                        <br/>
                        <button onclick="$('.advanced-date-filter').toggle()" class="btn btn-sm btn-link pl-0" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show advanced')?></button>
                    </div>

                    <div class="col-4 advanced-date-filter" style="display: none">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range start value')?></label>
                            <select name="date_type" id="report-date_type" class="form-control form-control-sm">
                                <option <?php if ($item->date_type == 'ndays') : ?>selected="selected"<?php endif;?> value="ndays"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last n days.')?></option>
                                <option <?php if ($item->date_type == 'lweek') : ?>selected="selected"<?php endif;?> value="lweek"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last n weeks.')?></option>
                                <option <?php if ($item->date_type == 'lmonth') : ?>selected="selected"<?php endif;?> value="lmonth"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last n months.')?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-8 advanced-date-filter" style="display: none">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Start from Days/Weeks/Months ago')?></label>
                                    <input type="number" required min="0" id="report-days" class="form-control form-control-sm" placeholder="days" name="days" value="<?php echo htmlspecialchars($item->days)?>" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Ends from Days/Weeks/Months ago')?></label>
                                    <input type="number" required min="0" class="form-control form-control-sm" id="report-days_end" placeholder="days" name="days_end" value="<?php echo htmlspecialchars($item->days_end)?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 pt-2">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select id="report-timefrom_hours" name="timefrom_hours" class="form-control form-control-sm">
                                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                                        <?php for ($i = 0; $i <= 23; $i++) : ?>
                                            <option value="<?php echo $i?>" <?php if (isset($item->params_array['input_form']['timefrom_hours']) && $item->params_array['input_form']['timefrom_hours'] === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                        <?php endfor;?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="report-timefrom_minutes" name="timefrom_minutes" class="form-control form-control-sm">
                                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                                            <option value="<?php echo $i?>" <?php if (isset($item->params_array['input_form']['timefrom_minutes']) && $item->params_array['input_form']['timefrom_minutes'] === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                        <?php endfor;?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="report-timefrom_seconds" name="timefrom_seconds" class="form-control form-control-sm">
                                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                                            <option value="<?php echo $i?>" <?php if (isset($item->params_array['input_form']['timefrom_seconds']) && $item->params_array['input_form']['timefrom_seconds'] === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                                        <?php endfor;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 pt-2">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select id="report-timeto_hours" name="timeto_hours" class="form-control form-control-sm">
                                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                                        <?php for ($i = 0; $i <= 23; $i++) : ?>
                                            <option value="<?php echo $i?>" <?php if (isset($item->params_array['input_form']['timeto_hours']) && $item->params_array['input_form']['timeto_hours'] === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                        <?php endfor;?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="report-timeto_minutes" name="timeto_minutes" class="form-control form-control-sm">
                                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                                            <option value="<?php echo $i?>" <?php if (isset($item->params_array['input_form']['timeto_minutes']) && $item->params_array['input_form']['timeto_minutes'] === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                        <?php endfor;?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="report-timeto_seconds" name="timeto_seconds" class="form-control form-control-sm">
                                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                                            <option value="<?php echo $i?>" <?php if (isset($item->params_array['input_form']['timeto_seconds']) && $item->params_array['input_form']['timeto_seconds'] === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                                        <?php endfor;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        (function (){
                            function updateDateRange(){
                                $.post(WWW_DIR_JAVASCRIPT + '/statistic/reportrange',{
                                    'date_type': $('#report-date_type').val(),
                                    'days': $('#report-days').val(),
                                    'days_end': $('#report-days_end').val(),
                                    'timefrom_hours': $('#report-timefrom_hours').val(),
                                    'timefrom_minutes': $('#report-timefrom_minutes').val(),
                                    'timefrom_seconds': $('#report-timefrom_seconds').val(),
                                    'timeto_hours': $('#report-timeto_hours').val(),
                                    'timeto_minutes': $('#report-timeto_minutes').val(),
                                    'timeto_seconds': $('#report-timeto_seconds').val()
                                }, function(data) {
                                    $('#report-sample-date-range').html(data);
                                });
                            }
                            $('#report-presets > span.action-image').click(function(){
                                $('#report-date_type').val($(this).attr('data-data_type'));
                                $('#report-days').val($(this).attr('data-days'));
                                $('#report-days_end').val($(this).attr('data-days_end'));
                                $('#report-timefrom_hours,#report-timefrom_minutes,#report-timefrom_seconds').val("");
                                $('#report-timeto_hours').val($(this).attr('data-hour'));
                                $('#report-timeto_minutes').val($(this).attr('data-minute'));
                                $('#report-timeto_seconds').val($(this).attr('data-seconds'));
                                updateDateRange();
                            });
                            $('#report-date_type,#report-days,#report-days_end,#report-timefrom_hours,#report-timefrom_minutes,#report-timefrom_seconds,#report-timeto_hours,#report-timeto_minutes,#report-timeto_seconds').change(function(){
                                updateDateRange();
                            });
                            updateDateRange();
                        })();
                    </script>
                    <div class="col-12 font-weight-bold" id="report-sample-date-range">

                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','The higher number the higher in the report list it will appear')?></label>
                            <input required maxlength="100" class="form-control form-control-sm" type="text" ng-non-bindable name="position" value="<?php echo htmlspecialchars($item->position)?>" />
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="export_action" value="doExport">
        <?php endif; ?>

        <div class="modal-footer">
            <div class="btn-group mr-2">
                <?php if (!(isset($updated) && $updated == true)) : ?>
                    <button type="submit" name="XLS" class="btn btn-primary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save')?></button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
            </div>
        </div>

    </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>