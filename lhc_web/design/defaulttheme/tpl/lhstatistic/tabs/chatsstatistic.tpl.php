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
	       <option value="2" <?php $input->groupby == 2 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Week');?></option>
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
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Invitation');?></label>
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'invitation_id',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose proactive invitation'),
                'selected_id'    => $input->invitation_id,
                'css_class'      => 'form-control form-control-sm',
                'list_function'  => 'erLhAbstractModelProactiveChatInvitation::getList'
            )); ?>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Bot');?></label>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'bot_ids[]',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select bot'),
                'selected_id'    => $input->bot_ids,
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params' => [],
                'list_function'  => 'erLhcoreClassModelGenericBotBot::getList'
            )); ?>
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

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group field');?></label>
            <select class="form-control form-control-sm" name="group_field">
                <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/filter/group_field.tpl.php'));?>
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row">
            <div class="col-4"><label><input type="checkbox" name="exclude_offline" value="<?php echo erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ?>" <?php $input->exclude_offline == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Exclude offline requests from charts')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="online_offline" value="<?php echo erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ?>" <?php $input->online_offline == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show only offline requests')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="no_operator" value="1" <?php $input->no_operator == true ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats without an operator')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="has_operator" value="1" <?php $input->has_operator == true ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats with an operator')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="with_bot" value="1" <?php $input->with_bot == true ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats which had a bot')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="without_bot" value="1" <?php $input->without_bot == true ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats which did not had a bot')?></label></div>
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/filter/statistic_chat_filter_multiinclude.tpl.php'));?>

    <div class="col-md-12">
        <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','What charts to display')?></h6>
        <div class="row">
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="active" <?php if (in_array('active',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chat numbers by status')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="unanswered" <?php if (in_array('unanswered',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unanswered chat numbers')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="msgtype" <?php if (in_array('msgtype',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Message types')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="proactivevsdefault" <?php if (in_array('proactivevsdefault',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive chats number vs visitors initiated')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="waitmonth" <?php if (in_array('waitmonth',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average wait time in seconds (maximum of 10 minutes)')?></label></div>
            <div class="col-4"><label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Usefull if you prefill usernames always')?>"><input type="checkbox" name="chart_type[]" value="nickgroupingdate" <?php if (in_array('nickgroupingdate',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unique group field records grouped by date')?></label></div>
            <div class="col-4"><label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Usefull if you prefill usernames always')?>"><input type="checkbox" name="chart_type[]" value="nickgroupingdatenick" <?php if (in_array('nickgroupingdatenick',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats number grouped by date and group field')?></label></div>
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

<?php if (isset($_GET['doSearch'])) : ?> 
<script type="text/javascript">
	function redrawAllCharts(){
			drawChartPerMonth();
            drawChartByNickMonth();
	};

    // Define a plugin to provide data labels
    Chart.plugins.register({
        afterDatasetsDraw: function(chart, easing) {
            // To only draw at the end of animation, check for easing === 1
            var ctx = chart.ctx;
            chart.data.datasets.forEach(function (dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function(element, index) {
                        // Draw the text in black, with the specified font
                        var dataString = dataset.data[index].toString();
                        if (dataString !== '0')
                        {
                            ctx.fillStyle = 'rgb(0, 0, 0)';
                            var fontSize = 11;
                            var fontStyle = 'normal';
                            var fontFamily = 'Arial';
                            ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                            // Just naively convert to string for now

                            // Make sure alignment settings are correct
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            var padding = 5;
                            var position = element.tooltipPosition();
                            ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                        }
                    });
                }
            });
        }
    });

    Chart.Legend.prototype.afterFit = function() {
        this.height = this.height + 10;
    };

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

    function drawChartByNickMonth() {
        <?php if (isset($nickgroupingdate) && !empty($nickgroupingdate)) : ?>
        <?php if (in_array('nickgroupingdate',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($nickgroupingdate as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unique records');?>',
                    backgroundColor: '#3366cc',
                    borderColor: '#3366cc',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($nickgroupingdate as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['unique']; $key++; endforeach;?>]
                }
            ]
        };

        var ctx = document.getElementById("chart_nickgroupingdate").getContext("2d");
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
                    }],
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
        <?php endif; ?>

        <?php if (isset($nickgroupingdatenick) && !empty($nickgroupingdatenick)) : ?>

        <?php if (in_array('nickgroupingdatenick',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
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

        var ctx = document.getElementById("chart_nickgroupingdatenick").getContext("2d");
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
    }

	function drawChartPerMonth() {

        <?php if (in_array('active',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Active');?>',
                    backgroundColor: '#dc3912',
                    borderColor: '#dc3912',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['active']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['operators']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>',
                    backgroundColor: '#109618',
                    borderColor: '#109618',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['pending']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Closed');?>',
                    backgroundColor: '#3366cc',
                    borderColor: '#3366cc',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['closed']; $key++; endforeach;?>]
                },
            ]
        };

        var ctx = document.getElementById("chart_div_per_month").getContext("2d");
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
                    }],
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

        <?php if (in_array('unanswered',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ; echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['unanswered']; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_per_month_unanswered');
        <?php endif; ?>

        <?php if (in_array('waitmonth',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : ; echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_per_month_wait_time');
        <?php endif; ?>

        <?php if (in_array('proactivevsdefault',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive');?>',
                    backgroundColor: '#dc3912',
                    borderColor: '#dc3912',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['chatinitproact']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors initiated');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['chatinitdefault']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("chart_type_div_per_month").getContext("2d");
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

        <?php if (in_array('msgtype',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date($groupby,$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_user']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>',
                    backgroundColor: '#dc3912',
                    borderColor: '#dc3912',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_operator']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','System');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_system']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("chart_type_div_msg_type").getContext("2d");
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
	}
	

	$( document ).ready(function() {
		redrawAllCharts();
	});
				
</script> 


<?php if (in_array('active',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chats_number_by_statuses.tpl.php'));?></h5>
<canvas id="chart_div_per_month"></canvas>
<?php endif; ?>

<?php if (in_array('nickgroupingdate',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/nickgroupingdate.tpl.php'));?></h5>
    <canvas id="chart_nickgroupingdate"></canvas>
<?php endif; ?>

<?php if (in_array('nickgroupingdatenick',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
    <hr>
    <h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/nickgroupingdatenick.tpl.php'));?></h5>
    <canvas id="chart_nickgroupingdatenick"></canvas>
<?php endif; ?>

<?php if (in_array('proactivevsdefault',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/proactive_chats_number_vs_visitors_initiated.tpl.php'));?></h5>
<canvas id="chart_type_div_per_month"></canvas>
<?php endif; ?>

<?php if (in_array('msgtype',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/messages_types.tpl.php'));?></h5>
<canvas id="chart_type_div_msg_type"></canvas>
<?php endif; ?>

<?php if (in_array('waitmonth',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/avg_wait_time_in_seconds_max_10_mininutes.tpl.php'));?></h5>
<canvas id="chart_div_per_month_wait_time"></canvas>
<?php endif; ?>

<?php if (in_array('unanswered',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats_numbers.tpl.php'));?></h5>
<canvas id="chart_div_per_month_unanswered"></canvas>
<?php endif; ?>

<?php else : ?>
<br/>
<div class="alert alert-info">
  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
</div>
<?php endif; ?>