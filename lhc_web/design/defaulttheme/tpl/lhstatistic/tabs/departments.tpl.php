<form action="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/departments" method="get" autocomplete="off">

    <div class="row form-group">

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department')?></label>
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

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentGroupFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day interval to include from');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="timeintervalfrom_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour')?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeintervalfrom_hours) && $input->timeintervalfrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timeintervalfrom_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute')?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeintervalfrom_minutes) && $input->timeintervalfrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day interval to include to');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="timeintervalto_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour')?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeintervalto_hours) && $input->timeintervalto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timeintervalto_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute')?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeintervalto_minutes) && $input->timeintervalto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
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
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour')?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timefrom_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute')?></option>
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
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timeto_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />

        <?php if (!empty($departmentstats)) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(xls)/1/(tab)/departments<?php echo $input_append?>" class="btn btn-secondary">XLS</a>
        <?php endif; ?>
    </div>



    <script>
        $(function() {
            $('#id_timefrom,#id_timeto').fdatepicker({
                format: 'yyyy-mm-dd'
            });
            $('.btn-block-department').makeDropdown();
        });
    </script>
</form>

<?php if (!empty($departmentstats)) : ?>

<hr>

<script>
function drawDepartmentStats(){
    var barChartData = {
        labels: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),'\''.$key . 'h. - ' . ($key+1) . 'h.'.'\'';$counter++; endforeach;?>],
        datasets: [{
            label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')?>',
            backgroundColor: '#109618',
            borderColor: '#109618',
            borderWidth: 1,
            data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][0]['perc']) ? $data['stats_formated'][0]['perc'] : 0);$counter++; endforeach;?>]
        },
        {
            label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')?>',
            backgroundColor: '#000000',
            borderColor: '#000000',
            borderWidth: 1,
            data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][1]['perc']) ? $data['stats_formated'][1]['perc'] : 0);$counter++; endforeach;?>]
        },
        {
            label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')?>',
            backgroundColor: '#961614',
            borderColor: '#961614',
            borderWidth: 1,
            data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][2]['perc']) ? $data['stats_formated'][2]['perc'] : 0);$counter++; endforeach;?>]
        },
        {
            label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')?>',
            backgroundColor: '#bfb713',
            borderColor: '#bfb713',
            borderWidth: 1,
            data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][3]['perc']) ? $data['stats_formated'][3]['perc'] : 0);$counter++; endforeach;?>]
        }]
    };



    var ctx = document.getElementById("departments-stats").getContext("2d");
    var myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
            legend: {
               // display : false,
               // position: 'top',
            },
            layout: {
                padding: {
                    top: 20
                }
            },
            tooltips: {
                callbacks: {
                    label : function(param) {
                        var labelsTypes = {
                            0 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')?>',
                            1 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')?>',
                            2 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')?>',
                            3 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')?>',
                        };
                        return labelsTypes[param.datasetIndex] + ' ' + param.yLabel + '%';
                    }
                }
            },
            scales: {
                xAxes: [{
                    stacked: true,
                    ticks: {
                        fontSize: 11,
                        min: 0,
                        beginAtZero: true,
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true
                    },
                    max: 100
                }]
            },
            title: {
                display: false
            }
        }
    });

    var barChartData = {
        labels: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),'\''.$key . 'h. - ' . ($key+1) . 'h.'.'\'';$counter++; endforeach;?>],
        datasets: [{
            label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')?>',
            backgroundColor: '#109618',
            borderColor: '#109618',
            borderWidth: 1,
            data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][0]['perc']) ? $data['stats_formated'][0]['perc'] : 0);$counter++; endforeach;?>]
        },
            {
                label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')?>',
                backgroundColor: '#000000',
                borderColor: '#000000',
                borderWidth: 1,
                data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][1]['perc']) ? $data['stats_formated'][1]['perc'] : 0);$counter++; endforeach;?>]
            },
            {
                label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')?>',
                backgroundColor: '#961614',
                borderColor: '#961614',
                borderWidth: 1,
                data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][2]['perc']) ? $data['stats_formated'][2]['perc'] : 0);$counter++; endforeach;?>]
            },
            {
                label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')?>',
                backgroundColor: '#bfb713',
                borderColor: '#bfb713',
                borderWidth: 1,
                data: [<?php $counter = 0; foreach ($departmentstats['hour_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['stats_formated'][3]['perc']) ? $data['stats_formated'][3]['perc'] : 0);$counter++; endforeach;?>]
            }]
    };

    <?php
        $coloursMap = array(
            0 => '#109618',
            1 => '#000000',
            2 => '#961614',
            3 => '#bfb713'
        );

        $statusMap = array(
            0 => "'" .erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')."'",
            1 => "'" .erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')."'",
            2 => "'" .erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')."'",
            3 => "'" .erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')."'"
        );
    ?>

    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [ <?php $counter = 0; foreach ($departmentstats['global_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['perc']) ? $data['perc'] : 0);$counter++; endforeach;?>],
                backgroundColor: [
                    <?php $counter = 0; foreach ($departmentstats['global_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['status']) ? "'".$coloursMap[$data['status']]."'" : '\'#fff\'');$counter++; endforeach;?>
                ],
                label: 'Dataset 1'
            }],
            labels: [
                <?php $counter = 0; foreach ($departmentstats['global_stats'] as $key => $data) : echo ($counter > 0 ? ',' : ''),(isset($data['status']) ? $statusMap[$data['status']] : '\'#fff\'');$counter++; endforeach;?>
            ]
        },
        options: {
            tooltips: {
                callbacks: {
                    label : function(param) {
                        var labelsTypes = {
                            0 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')?>',
                            1 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')?>',
                            2 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')?>',
                            3 : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')?>',
                        };

                        return labelsTypes[param.index] + ' ' + config.data.datasets[0]['data'][param.index] + '%';
                    }
                }
            },
            responsive: true
        }
    };

    var ctx = document.getElementById('departments-stats-pie').getContext('2d');
    var pie = new Chart(ctx, config);

}
</script>

<div class="row">
    <div class="col-4">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Date')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')?></th>
                </tr>
            </thead>
            <?php foreach ($departmentstats['day_stats'] as $day)  : ?>
                <tr>
                    <td><?php echo date(erLhcoreClassModule::$dateFormat,$day['time'])?></td>
                    <td title="<?php echo isset($day['stats_formated'][0]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][0]['seconds']) : 0?>"><?php echo isset($day['stats_formated'][0]['perc']) ? $day['stats_formated'][0]['perc'] : 0?>% </td>
                    <td title="<?php echo isset($day['stats_formated'][1]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][1]['seconds']) : 0?>"><?php echo isset($day['stats_formated'][1]['perc']) ? $day['stats_formated'][1]['perc'] : 0?>% </td>
                    <td title="<?php echo isset($day['stats_formated'][2]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][2]['seconds']) : 0?>"><?php echo isset($day['stats_formated'][2]['perc']) ? $day['stats_formated'][2]['perc'] : 0?>% </td>
                    <td title="<?php echo isset($day['stats_formated'][3]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][3]['seconds']) : 0?>"><?php echo isset($day['stats_formated'][3]['perc']) ? $day['stats_formated'][3]['perc'] : 0?>% </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Hour')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Online')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Disabled')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Overloaded')?></th>
                    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Offline')?></th>
                </tr>
            </thead>
            <?php foreach ($departmentstats['hour_stats'] as $hour => $day)  : ?>
                <tr>
                    <td><?php echo $hour?>h. - <?php echo $hour+1?>h.</td>
                    <td title="<?php echo isset($day['stats_formated'][0]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][0]['seconds']) : 0?>">
                            <?php echo isset($day['stats_formated'][0]['perc']) ? $day['stats_formated'][0]['perc'] : 0?>%
                    </td>
                    <td title="<?php echo isset($day['stats_formated'][3]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][3]['seconds']) : 0?>">
                            <?php echo isset($day['stats_formated'][1]['perc']) ? $day['stats_formated'][1]['perc'] : 0?>%
                    </td>
                    <td title="<?php echo isset($day['stats_formated'][2]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][2]['seconds']) : 0?>">
                            <?php echo isset($day['stats_formated'][2]['perc']) ? $day['stats_formated'][2]['perc'] : 0?>%
                    </td>
                    <td title="<?php echo isset($day['stats_formated'][3]['seconds']) ? erLhcoreClassChat::formatSeconds($day['stats_formated'][3]['seconds']) : 0?>">
                            <?php echo isset($day['stats_formated'][3]['perc']) ? $day['stats_formated'][3]['perc'] : 0?>%
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
    <div class="col-5">
        <canvas id="departments-stats"></canvas>
    </div>
    <div class="col-3">
        <canvas id="departments-stats-pie"></canvas>
    </div>
</div>

<script>drawDepartmentStats()</script>

<?php else : ?>
    <br/>
    <div class="alert alert-info">
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
    </div>
<?php endif; ?>