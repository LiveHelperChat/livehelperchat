<form action="" method="get" autocomplete="off">
    <div class="row form-group">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                    'selected_id'    => $input->user_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name_official',
                    'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(),
                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
                    'selected_id'    => $input->group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(false, true),
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group by');?></label>
                <select name="groupby" class="form-control form-control-sm">
                    <option value="0" <?php $input->groupby == 0 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Month');?></option>
                    <option value="1" <?php $input->groupby == 1 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day');?></option>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
                    'selected_id'    => $input->department_group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentGroupFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group field');?></label>
                <select class="form-control form-control-sm" name="group_field">
                    <option value="user_id" <?php $input->group_field == '' || $input->group_field == 'user_id' ? print 'selected="selected"' : '' ?>>User</option>
                    <option value="dep_id" <?php $input->group_field == 'dep_id' ? print 'selected="selected"' : '' ?>>Department</option>
                    <option value="mailbox_id" <?php $input->group_field == 'mailbox_id' ? print 'selected="selected"' : '' ?>>Mailbox</option>
                    <option value="response_type" <?php $input->group_field == 'response_type' ? print 'selected="selected"' : '' ?>>Messages by response type</option>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="timefrom_hours" class="form-control form-control-sm">
                            <option value="">Select hour</option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timefrom_minutes" class="form-control form-control-sm">
                            <option value="">Select minute</option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="timeto_hours" class="form-control form-control-sm">
                            <option value="">Select hour</option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timeto_minutes" class="form-control form-control-sm">
                            <option value="">Select minute</option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-4"><label><input type="checkbox" name="no_operator" value="1" <?php $input->no_operator == true ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats without an operator')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="has_operator" value="1" <?php $input->has_operator == true ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats with an operator')?></label></div>
            </div>
        </div>

        <div class="col-md-12">
            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','What charts to display')?></h6>
            <div class="row">
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mmsgperinterval" <?php if (in_array('mmsgperinterval',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of messages per interval')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mmsgperuser" <?php if (in_array('mmsgperuser',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of messages per user')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mmsgperdep" <?php if (in_array('mmsgperdep',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of messages per department')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mmintperdep" <?php if (in_array('mmintperdep',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average duration of interactions by department')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mmintperuser" <?php if (in_array('mmintperuser',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average duration of interactions per user')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mavgwaittime" <?php if (in_array('mavgwaittime',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average wait time')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="mattrgroup" <?php if (in_array('mattrgroup',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Messages grouped by date and group field')?></label></div>
                <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="msgperhour" <?php if (in_array('msgperhour',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Messages number per hour')?></label></div>
            </div>
        </div>
    </div>

    <input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />

    <script>
        $(function() {
            $('#id_timefrom,#id_timeto').fdatepicker({
                format: 'yyyy-mm-dd'
            });
            $('.btn-block-department').makeDropdown();
        });
    </script>

</form>

<script>
    function redrawAllCharts(){
        drawChartPerMonth();
    };

    function toHHMMSS(ts) {
        var sec_num = parseInt(ts, 10); // don't forget the second param
        var hours   = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        return hours+':'+minutes+':'+seconds;
    }

    function drawChartPerMonth() {
        <?php if (in_array('mmsgperinterval',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Send messages');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['send']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Responded to messages');?>',
                    backgroundColor: '#44d800',
                    borderColor: '#44d800',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['normal']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','No response required');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['notrequired']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unresponded');?>',
                    backgroundColor: '#fe438c',
                    borderColor: '#fe438c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['unresponded']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("mmsgperinterval").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('mmsgperuser',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($mmsgperuser as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars($data['user_id'] > 0 ? erLhcoreClassModelUser::fetch($data['user_id'],true)->name_official : 'Not assigned',ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of messages');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperuser as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['total_records']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("mmsgperuser").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('mmsgperdep',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($mmsgperdep as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars($data['dep_id'] > 0 ? (string)erLhcoreClassModelDepartament::fetch($data['dep_id'],true) : 'Not assigned',ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of messages per department');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperdep as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['total_records']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("mmsgperdep").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('mmintperdep',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($mmintperdep as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars($data['dep_id'] > 0 ? (string)erLhcoreClassModelDepartament::fetch($data['dep_id'],true) : 'Not assigned',ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average interaction time');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmintperdep as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['interaction_time']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("mmintperdep").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label : function(param) { return toHHMMSS(param.yLabel); }
                    }
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('mmintperuser',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($mmintperuser as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars($data['user_id'] > 0 ? erLhcoreClassModelUser::fetch($data['user_id'],true)->name_official : 'Not assigned',ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average interaction time');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmintperuser as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['interaction_time']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("mmintperuser").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label : function(param) { return toHHMMSS(param.yLabel); }
                    }
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('mavgwaittime',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average wait time');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($mmsgperinterval as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['avg_wait_time']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("mavgwaittime").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label : function(param) { return toHHMMSS(param.yLabel); }
                    }
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (isset($nickgroupingdatenick) && !empty($nickgroupingdatenick)) : ?>

        <?php if (in_array('mattrgroup',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($nickgroupingdatenick['labels'] as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                <?php foreach ($nickgroupingdatenick['data'] as $data) : ?>
                {
                    data: [<?php echo implode(',',$data['data'])?>],
                    backgroundColor: [<?php echo implode(',',$data['color'])?>],
                    labels: [<?php echo implode(',',$data['nick'])?>]
                },
                <?php endforeach; ?>
            ]
        };

        var ctx = document.getElementById("mattrgroup").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var index = tooltipItem.index;
                            if (dataset.data[index] != 0) {
                                return  dataset.data[index] + ': ' + (dataset.labels[index] == '' ? 'Unknown' : dataset.labels[index]);
                            }
                        }
                    }
                },
                legend: {
                    display: false,
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        //stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        //stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array('msgperhour',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
            <?php if (isset($msgperhour['total'])) : ?>
            var barChartData = {
                labels: [<?php $key = 0; foreach ($msgperhour['byday'] as $hour => $chatsNumber) : echo ($key > 0 ? ',' : ''),'\''.$hour.'\'';$key++; endforeach;?>],
                datasets: [{
                    type: 'line',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 2,
                    fill: false,
                    data: [<?php $key = 0; foreach ($msgperhour['byday'] as $hour => $chatsNumber) : echo ($key > 0 ? ',' : ''),'\'' . round($chatsNumber,2) . '\'';$key++; endforeach;?>]
                }<?php if (isset($msgperhour['bydayavgresponse'])) : ?>,
                    {
                        type: 'bar',
                        backgroundColor: '#89e089',
                        data: [<?php $key = 0; $timesEvent = array(); foreach ($msgperhour['bydayavgresponse'] as $hour => $chatsData) : echo ($key > 0 ? ',' : ''),'\'' . $chatsData . '\'';$key++; endforeach;?>],
                        borderColor: 'white',
                        borderWidth: 2
                    }<?php endif; ?>]
            };

        var ctx = document.getElementById("msgperhour").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    display : false,
                    position: 'top',
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                tooltips: {
                    callbacks: {
                        label : function(param) {
                            var times = <?php echo isset($timesEvent) ? json_encode($timesEvent) : '[]';?>;
                            if (param.datasetIndex == 0) {
                                return '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average number of messages');?>: ' + param.yLabel;
                            } else {
                                return '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average response time');?>: '+param.yLabel;
                            }
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });

            <?php endif; ?>
        <?php endif; ?>
    }


    function drawBasicChart(data, id) {
        var ctx = document.getElementById(id).getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                legend: {
                    display : false,
                    position: 'top',
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
    }

    $( document ).ready(function() {
        redrawAllCharts();
        drawChartWorkload();
    });

</script>

<?php if (in_array('mmsgperinterval',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Number of messages</h5>
    <canvas id="mmsgperinterval"></canvas>
<?php endif; ?>

<?php if (in_array('mmsgperuser',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Number of messages per user</h5>
    <canvas id="mmsgperuser"></canvas>
<?php endif; ?>

<?php if (in_array('mmsgperdep',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Number of messages per department</h5>
    <canvas id="mmsgperdep"></canvas>
<?php endif; ?>

<?php if (in_array('mmintperdep',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Average duration of interactions by department. Max 10 minutes.</h5>
    <canvas id="mmintperdep"></canvas>
<?php endif; ?>

<?php if (in_array('mmintperuser',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Average duration of interactions per user.  Max 10 minutes.</h5>
    <canvas id="mmintperuser"></canvas>
<?php endif; ?>

<?php if (in_array('mavgwaittime',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Average wait time. Max 10 minutes.</h5>
    <canvas id="mavgwaittime"></canvas>
<?php endif; ?>

<?php if (in_array('mattrgroup',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Number of messages grouped by attribute</h5>
    <canvas id="mattrgroup"></canvas>
<?php endif; ?>

<?php if (in_array('msgperhour',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5>Messages number per hour</h5>
    <canvas id="msgperhour"></canvas>
<?php endif; ?>