<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
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
                    <div class="col-6">
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
                                <span data-data_type="ndays" data-days="1" data-days_end="1" data-hour="23" data-minute="59" data-seconds="59" class="bg-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous Day')?></span>
                                <span data-data_type="lweek" data-days="1" data-days_end="1" data-hour="23" data-minute="59" data-seconds="59" class="bg-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous Week')?></span>
                                <span data-data_type="lmonth" data-days="1" data-days_end="1" data-hour="23" data-minute="59" data-seconds="59" class="bg-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Previous Month')?></span>
                                <span data-data_type="ndays" data-days="0" data-days_end="0" data-hour="" data-minute="" data-seconds="" class="bg-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Current Day')?></span>
                                <span data-data_type="lweek" data-days="0" data-days_end="0" data-hour="" data-minute="" data-seconds="" class="bg-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Current Week')?></span>
                                <span data-data_type="lmonth" data-days="0" data-days_end="0" data-hour="" data-minute="" data-seconds="" class="bg-secondary badge action-image"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Current month')?></span>
                                <br/>
                                <button onclick="$('.advanced-date-filter').toggle()" class="btn btn-sm btn-link ps-0" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show advanced')?></button>
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
                            <div class="col-12 pt-2">
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
                            <div class="col-12 pt-2">
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
                            <div class="col-12 fw-bold" id="report-sample-date-range">

                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','The higher number the higher in the report list it will appear')?></label>
                                    <input required maxlength="100" class="form-control form-control-sm" type="text" ng-non-bindable name="position" value="<?php echo htmlspecialchars($item->position)?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Send report to')?></label>
                            <input maxlength="100" placeholder="example1@example.org,example2@example.org" class="form-control form-control-sm" type="text" ng-non-bindable name="send_to" value="<?php echo htmlspecialchars(isset($item->recurring_options_array['send_to']) ? $item->recurring_options_array['send_to'] : '')?>" />
                        </div>
                        <div role="tabepanel">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#daily"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Daily report')?></a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#weekly"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Weekly report')?></a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#monthly"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Monthly report')?></a>
                                </li>
                            </ul>
                            <div class="tab-content pt-2" id="myTabContent">
                                <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="row">
                                        <?php for ($i = 0; $i < 6; $i++) : ?>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <?php
                                                        if (isset($item->recurring_options_array['send_daily'][$i * 3 + 1])) {
                                                            $minutesStart = str_pad(substr($item->recurring_options_array['send_daily'][$i * 3 + 1],-2),2,'0', STR_PAD_LEFT);
                                                            $hoursStart = str_pad(substr($item->recurring_options_array['send_daily'][$i * 3 + 1],0,strlen($item->recurring_options_array['send_daily'][$i * 3 + 1]) - 2), 2, '0', STR_PAD_LEFT);
                                                        } else {
                                                            $hoursStart = $minutesStart = '00';
                                                        }
                                                    ?>
                                                    <label><input type="checkbox" name="send_daily_active[]" <?php isset($item->recurring_options_array['send_daily_active']) && in_array($i * 3 + 1,$item->recurring_options_array['send_daily_active']) ? print 'checked="checked"' : ''?> value="<?php echo $i * 3 + 1?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active')?></label>
                                                    <input name="send_daily[<?php echo $i * 3 + 1?>]" value="<?php echo htmlspecialchars($hoursStart . ':' . $minutesStart)?>" type="time" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <?php
                                                        if (isset($item->recurring_options_array['send_daily'][$i * 3 + 2])) {
                                                            $minutesStart = str_pad(substr($item->recurring_options_array['send_daily'][$i * 3 + 2],-2),2,'0', STR_PAD_LEFT);
                                                            $hoursStart = str_pad(substr($item->recurring_options_array['send_daily'][$i * 3 + 2],0,strlen($item->recurring_options_array['send_daily'][$i * 3 + 2]) - 2), 2, '0', STR_PAD_LEFT);
                                                        } else {
                                                            $minutesStart = $hoursStart = '00';
                                                        }
                                                    ?>
                                                    <label><input type="checkbox" name="send_daily_active[]" <?php isset($item->recurring_options_array['send_daily_active']) && in_array($i * 3 + 2,$item->recurring_options_array['send_daily_active']) ? print 'checked="checked"' : ''?> value="<?php echo $i * 3 + 2?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active')?></label>
                                                    <input name="send_daily[<?php echo $i * 3 + 2?>]" value="<?php echo htmlspecialchars($hoursStart . ':' . $minutesStart)?>" type="time" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <?php
                                                        if (isset($item->recurring_options_array['send_daily'][$i * 3 + 3])) {
                                                            $minutesStart = str_pad(substr($item->recurring_options_array['send_daily'][$i * 3 + 3], -2), 2, '0', STR_PAD_LEFT);
                                                            $hoursStart = str_pad(substr($item->recurring_options_array['send_daily'][$i * 3 + 3], 0, strlen($item->recurring_options_array['send_daily'][$i * 3 + 3]) - 2), 2, '0', STR_PAD_LEFT);
                                                        } else {
                                                            $minutesStart = $hoursStart = '00';
                                                        }
                                                    ?>
                                                    <label><input type="checkbox" name="send_daily_active[]" <?php isset($item->recurring_options_array['send_daily_active']) && in_array($i * 3 + 3,$item->recurring_options_array['send_daily_active']) ? print 'checked="checked"' : ''?> value="<?php echo $i * 3 + 3?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active')?></label>
                                                    <input name="send_daily[<?php echo $i * 3 + 3?>]" value="<?php echo htmlspecialchars($hoursStart . ':' . $minutesStart)?>" type="time" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="weekly" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <?php for ($i = 1; $i <= 7; $i++) : ?>
                                            <div class="col-2">
                                                <label><input type="checkbox" <?php isset($item->recurring_options_array['send_weekly_active']) && in_array($i, $item->recurring_options_array['send_weekly_active']) ? print 'checked="checked"' : ''?> name="send_weekly_active[]" value="<?php echo $i?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active')?></label>
                                            </div>
                                            <div class="col-5">
                                                <select class="form-control form-control-sm" name="send_weekly_day[<?php echo $i?>]">
                                                    <option value="1" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 1 ? print 'selected="selected"' : ''?> >Monday</option>
                                                    <option value="2" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 2 ? print 'selected="selected"' : ''?> >Tuesday</option>
                                                    <option value="3" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 3 ? print 'selected="selected"' : ''?> >Wednesday</option>
                                                    <option value="4" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 4 ? print 'selected="selected"' : ''?> >Thursday</option>
                                                    <option value="5" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 5 ? print 'selected="selected"' : ''?> >Friday</option>
                                                    <option value="6" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 6 ? print 'selected="selected"' : ''?> >Saturday</option>
                                                    <option value="7" <?php isset($item->recurring_options_array['send_weekly_day'][$i]) && $item->recurring_options_array['send_weekly_day'][$i] == 7 ? print 'selected="selected"' : ''?> >Sunday</option>
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <?php
                                                    if (isset($item->recurring_options_array['send_weekly_time'][$i])) {
                                                        $minutesStart = str_pad(substr($item->recurring_options_array['send_weekly_time'][$i],-2),2,'0', STR_PAD_LEFT);
                                                        $hoursStart = str_pad(substr($item->recurring_options_array['send_weekly_time'][$i],0,strlen($item->recurring_options_array['send_weekly_time'][$i]) - 2), 2, '0', STR_PAD_LEFT);
                                                    } else {
                                                        $minutesStart = $hoursStart = '00';
                                                    }
                                                    ?>
                                                    <input name="send_weekly_time[<?php echo $i?>]" value="<?php echo htmlspecialchars($hoursStart . ':' . $minutesStart)?>" type="time" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                    <?php for ($month = 1; $month <= 6; $month++) : ?>
                                        <div class="col-2">
                                            <label><input <?php isset($item->recurring_options_array['send_monthly_active']) && in_array($month, $item->recurring_options_array['send_monthly_active']) ? print 'checked="checked"' : ''?> name="send_monthly_active[]" type="checkbox" value="<?php echo $month?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active')?></label>
                                        </div>
                                        <div class="col-5">
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day of the month')?></label>
                                            <select name="send_month_day[<?php echo $month?>]" class="form-control form-control-sm">
                                                <?php for ($i = 1; $i <= 31; $i++) : ?>
                                                    <option <?php isset($item->recurring_options_array['send_month_day'][$month]) && $item->recurring_options_array['send_month_day'][$month] == $i ? print 'selected="selected"' : ''?> value="<?php echo $i?>"><?php echo $i?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Time')?></label>
                                            <div class="form-group">
                                                <?php
                                                if (isset($item->recurring_options_array['send_month_time'][$month])) {
                                                    $minutesStart = str_pad(substr($item->recurring_options_array['send_month_time'][$month],-2),2,'0', STR_PAD_LEFT);
                                                    $hoursStart = str_pad(substr($item->recurring_options_array['send_month_time'][$month],0,strlen($item->recurring_options_array['send_month_time'][$month]) - 2), 2, '0', STR_PAD_LEFT);
                                                } else {
                                                    $minutesStart = $hoursStart = '00';
                                                }
                                                ?>
                                                <input name="send_month_time[<?php echo $month?>]" value="<?php echo htmlspecialchars($hoursStart . ':' . $minutesStart)?>" type="time" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <input type="hidden" name="export_action" value="doExport">
            <input type="hidden" id="id_save_action" name="report_save_action" value="update">
        <?php endif; ?>

        <div class="modal-footer">
            <div class="btn-group me-2">
                <?php if (!(isset($updated) && $updated == true)) : ?>
                    <button type="submit" name="savePresent" onclick="$('#id_save_action').val('update')" class="btn btn-primary btn-sm"><span class="material-icons">save</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save')?></button>
                    <?php if ($item->id > 0) : ?>
                        <button type="submit" onclick="$('#id_save_action').val('new')" name="saveNew" class="btn btn-secondary btn-sm"><span class="material-icons">content_copy</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save as new')?></button>
                    <?php endif; ?>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><span class="material-icons">close</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
            </div>
        </div>

    </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>