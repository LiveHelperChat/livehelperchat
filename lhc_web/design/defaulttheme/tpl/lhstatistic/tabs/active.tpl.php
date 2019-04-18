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
               'list_function'  => 'erLhcoreClassModelGroup::getList'
            )); ?>
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
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
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
    	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
           <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
               'input_name'     => 'department_group_ids[]',
               'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
               'selected_id'    => $input->department_group_ids,
               'css_class'      => 'form-control',
               'display_name'   => 'name',
               'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
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

    <div class="col-md-12">
        <label><input type="checkbox" name="exclude_offline" value="<?php echo erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ?>" <?php $input->exclude_offline == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Exclude offline requests from charts')?></label>&nbsp;&nbsp;<label><input type="checkbox" name="online_offline" value="<?php echo erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ?>" <?php $input->online_offline == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ? print 'checked="checked"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show only offline requests')?></label>
    </div>

	<?php 
	/**
	 * Not implemented at the moment 
	<div class="col-md-3">	   
	    <br>
    	<label><input type="checkbox" value="on" name="comparetopast" <?php $input->comparetopast == 1 ? print 'checked="checked"' : ''?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Compare to past');?></label>    	
    </div>*/
	?>
</div>
	
	<input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
	
	<script>
	$(function() {
        $('.btn-block-department').makeDropdown();
		$('#id_timefrom,#id_timeto').fdatepicker({
			format: 'yyyy-mm-dd'
		});
	});
	</script>							
</form>

<?php if (isset($_GET['doSearch'])) : ?>

<script type="text/javascript">
	function redrawAllCharts(){
			drawChart();
			drawChartCountry();
			drawChartUser();
			drawChartPerMonth();
			drawChartWorkload();
			drawChartWorkloadHourly();
			drawChartUserMessages();
			drawChartUserAVGWaitTime();
			drawChartUserAverage();
            drawChartDepartmnent();
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

	function drawChart() {
	  <?php if (!empty($userStats['thumbsup'])) : ?>
        var barChartData = {
            labels: [<?php foreach ($userStats['thumbsup'] as $key => $data) : $nameUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars((is_object($nameUser) ? $nameUser->name_official : '-'),ENT_QUOTES).'\''; endforeach;?>],
            datasets: [{
                label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Thumbs up')?>',
                backgroundColor: '#109618',
                borderColor: '#109618',
                borderWidth: 1,
                data: [<?php foreach ($userStats['thumbsup'] as $key => $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; endforeach;?>]
            }]
        };

        var ctx = document.getElementById("chart_div_upvotes_canvas").getContext("2d");
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
                    display: true,
                    text: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_thumbs_up.tpl.php'));?>'
                }
            }
        });
	  <?php endif;?>

	  <?php if (!empty($userStats['thumbdown'])) : ?>
        var barChartData = {
            labels: [<?php foreach ($userStats['thumbdown'] as $key => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars(erLhcoreClassModelUser::fetch($data['user_id'],true)->name_official,ENT_QUOTES).'\''; endforeach;?>],
            datasets: [{
                label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Thumbs down')?>',
                backgroundColor: '#f497a9',
                borderColor: '#f497a9',
                borderWidth: 1,
                data: [<?php foreach ($userStats['thumbdown'] as $key => $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; endforeach;?>]
            }]
        };
        var ctx = document.getElementById("chart_div_downvotes_canvas").getContext("2d");
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
                    display: true,
                    text: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_thumbs_down.tpl.php'));?>'
                }
            }
        });
	  <?php endif;?>

	  <?php if (!empty($subjectsStatistic)) : ?>
	    var barChartData = {
            labels: [<?php foreach ($subjectsStatistic as $key => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars((string)erLhAbstractModelSubject::fetch($data['subject_id'],true),ENT_QUOTES).'\''; endforeach;?>],
            datasets: [{
                label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of chats')?>',
                backgroundColor: '#4bc044',
                borderColor: '#4bc044',
                borderWidth: 1,
                data: [<?php foreach ($subjectsStatistic as $key => $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_subjects_statistic');
      <?php endif; ?>
	};
	
	function drawChartCountry() {	
		<?php if (!empty($countryStats)) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($countryStats as $data) : echo ($key > 0 ? ',' : ''),'\''.$data['country_name'].'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($countryStats as $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_country');
		<?php endif;?>					  
	};
	
	function drawChartUser() {	
		<?php if (!empty($userChatsStats)) : ?>

                var barChartData = {
                    labels: [<?php $key = 0; foreach ($userChatsStats as $data) : $obUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars((is_object($obUser) ? $obUser->name_official : $data['user_id']),ENT_QUOTES).'\'';$key++; endforeach;?>],
                    datasets: [{
                        backgroundColor: '#36c',
                        borderColor: '#36c',
                        borderWidth: 1,
                        data: [<?php $key = 0; foreach ($userChatsStats as $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; $key++; endforeach;?>]
                    }]
                };
                drawBasicChart(barChartData,'chart_div_user');
		  <?php endif;?>						  
	};

	function drawChartDepartmnent() {
		<?php if (!empty($depChatsStats)) : ?>
            var barChartData = {
                        labels: [<?php $key = 0; foreach ($depChatsStats as $data) : $obUser = erLhcoreClassModelDepartament::fetch($data['dep_id'],true); echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars((is_object($obUser) ? $obUser->name : $data['dep_id']),ENT_QUOTES).'\'';$key++; endforeach;?>],
                        datasets: [{
                            backgroundColor: '#36c',
                            borderColor: '#36c',
                            borderWidth: 1,
                            data: [<?php $key = 0; foreach ($depChatsStats as $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; $key++; endforeach;?>]
                        }]
                    };
            drawBasicChart(barChartData,'chart_div_dep');
		  <?php endif;?>
	};
	
	function drawChartUserAverage() {	
		<?php if (!empty($userChatsAverageStats)) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($userChatsAverageStats as $data) :  $obUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars((is_object($obUser) ? $obUser->name_official : $data['user_id']),ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($userChatsAverageStats as $data) : echo ($key > 0 ? ',' : ''),round($data['avg_chat_duration']); $key++; endforeach;?>]
            }]
        };

        drawBasicChart(barChartData,'chart_div_avg_user');
        <?php endif;?>
	};

	function drawChartUserAVGWaitTime() {	
		<?php if (!empty($userWaitTimeByOperator)) : ?>


		var barChartData = {
            labels: [<?php $key = 0; foreach ($userWaitTimeByOperator as $data) : $obUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars((is_object($obUser) ? $obUser->name_official : $data['user_id']),ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($userWaitTimeByOperator as $data) : echo ($key > 0 ? ',' : ''),round($data['avg_wait_time']); $key++; endforeach;?>]
            }]
        };

		drawBasicChart(barChartData,'chart_div_user_wait_time');
		<?php endif;?>
	};
	
	function drawChartUserMessages() {
		<?php if (!empty($numberOfMsgByUser)) : ?>

        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfMsgByUser as $data) :
            $operator = '';
		    if ($data['user_id'] == 0) {
		    	$operator = 'Visitor';
		    } elseif ($data['user_id'] == -1) {
		    	$operator = 'System assistant';
		    } elseif ($data['user_id'] == -2) {
		    	$operator = 'Virtual assistant';
		    } else {
		        $operatorObj = erLhcoreClassModelUser::fetch($data['user_id'],true);
		        if (is_object($operatorObj) ) {
		    	   $operator = $operatorObj->name_official;
		        } else {
		           $operator = '['.$data['user_id'].']';
		        }
		    }; echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars($operator,ENT_QUOTES).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfMsgByUser as $data) : echo ($key > 0 ? ',' : ''),$data['number_of_chats']; $key++; endforeach;?>]
            }]
        };

        drawBasicChart(barChartData,'chart_div_user_msg');
		<?php endif;?>					  
	};
				
	function drawChartPerMonth() {	

		  // Chats number by statuses
		var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date('Y.m',$monthUnix).'\'';$key++; endforeach;?>],
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


		  // Chats number by unanswered chats
            var barChartData = {
                labels: [<?php $dataRange = array(); $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : $dataRange[] = '/(timefrom)/' . date('Y-m-d',$monthUnix) . '/(timeto)/' . date('Y-m-d',mktime(0,0,0,date('m',$monthUnix)+1,1,date('Y',$monthUnix))); echo ($key > 0 ? ',' : ''),'\''.date('Y.m',$monthUnix).'\'';$key++; endforeach;?>],
                datasets: [{
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['unanswered']; $key++; endforeach;?>]
                }]
            };

            var ctx = document.getElementById("chart_div_per_month_unanswered").getContext("2d");
            var myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    responsive: true,
                    layout: {
                        padding: {
                             top: 20
                        }
                    },
                    legend: {
                        display : false,
                        position: 'top',
                    },
                    onClick : function(evt) {
                        var activeElement = myBar.getElementAtEvent(evt);
                        var filter = <?php echo json_encode($dataRange)?>;
                        document.location = "<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('timefrom','timeto','timefrom_hours','timefrom_minutes','timeto_hours','timeto_minutes'))?>/(una)/1/" + filter[activeElement[0]._index];
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

		  // AVG Wait time
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date('Y.m',$monthUnix).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data; $key++; endforeach;?>]
            }]
        };

        drawBasicChart(barChartData,'chart_div_per_month_wait_time');

        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date('Y.m',$monthUnix).'\'';$key++; endforeach;?>],
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
                /*legend: {
                    display : false,
                    position: 'top',
                },*/
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

        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.date('Y.m',$monthUnix).'\'';$key++; endforeach;?>],
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
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Bot and Auto responder');?>',
                    backgroundColor: 'green',
                    borderColor: 'green',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_bot']; $key++; endforeach;?>]
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
	}
	
	function drawChartWorkload() {
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerHour['total'] as $hour => $chatsNumber) : echo ($key > 0 ? ',' : ''),'\''.$hour.'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerHour['total'] as $hour => $chatsNumber) : echo ($key > 0 ? ',' : ''),$chatsNumber; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_per_hour');
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

	function drawChartWorkloadHourly() {

        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerHour['byday'] as $hour => $chatsNumber) : echo ($key > 0 ? ',' : ''),'\''.$hour.'\'';$key++; endforeach;?>],
            datasets: [{
                type: 'line',
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 2,
                fill: false,
                data: [<?php $key = 0; foreach ($numberOfChatsPerHour['byday'] as $hour => $chatsNumber) : echo ($key > 0 ? ',' : ''),'\'' . round($chatsNumber,2) . '\'';$key++; endforeach;?>]
            }<?php if (isset($numberOfChatsPerHour['bydaymax'])) : ?>,
            {
                type: 'bar',
                backgroundColor: '#89e089',
                data: [<?php $key = 0; $timesEvent = array(); foreach ($numberOfChatsPerHour['bydaymax'] as $hour => $chatsData) : $timesEvent[] = date('Y-m-d',$chatsData['time']);echo ($key > 0 ? ',' : ''),'\'' . $chatsData['total_records'] . '\'';$key++; endforeach;?>],
                borderColor: 'white',
                borderWidth: 2
            }<?php endif; ?>]
        };

        var ctx = document.getElementById("chart_div_per_hour_by_hour").getContext("2d");
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
                                return '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average chats');?>: ' + param.yLabel;
                            } else {
                                return '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Max chats');?>: '+param.yLabel+', ' + times[param.index];
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
	}

	$( document ).ready(function() {
		redrawAllCharts();
	});
				
</script> 

<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/statistic_active_content_multiinclude.tpl.php'));?>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chats_number_by_statuses.tpl.php'));?></h5>
<canvas id="chart_div_per_month"></canvas>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/proactive_chats_number_vs_visitors_initiated.tpl.php'));?></h5>
<canvas id="chart_type_div_per_month"></canvas>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/messages_types.tpl.php'));?></h5>
<canvas id="chart_type_div_msg_type"></canvas>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/avg_wait_time_in_seconds_max_10_mininutes.tpl.php'));?></h5>
<canvas id="chart_div_per_month_wait_time"></canvas>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats_numbers.tpl.php'));?></h5>
<canvas id="chart_div_per_month_unanswered"></canvas>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_per_hour_average_chat_duration_hour.tpl.php'));?><h5>
<canvas id="chart_div_per_hour_by_hour"></canvas>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_per_hour_average_chat_duration.tpl.php'));?>&nbsp;<?php echo $averageChatTime != null ? erLhcoreClassChat::formatSeconds($averageChatTime) : '(-)';?></h5>
<canvas id="chart_div_per_hour"></canvas>

<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_country.tpl.php'));?></h5>
<canvas id="chart_div_country"></canvas>


<?php if (!empty($userChatsStats)) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_user.tpl.php'));?></h5>
<canvas id="chart_div_user"></canvas>
<?php endif;?>

<?php if (!empty($depChatsStats)) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_dep.tpl.php'));?></h5>
<canvas id="chart_div_dep"></canvas>
<?php endif;?>

<?php if (!empty($numberOfMsgByUser)) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_messages_by_user.tpl.php'));?></h5>
<canvas id="chart_div_user_msg"></canvas>
<?php endif;?>

<?php if (!empty($userChatsAverageStats)) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/average_chat_duration_by_user.tpl.php'));?> <a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?><?php echo $urlappend?>?xmlavguser=1" target="_blank" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','All operators statistic will be downloaded')?>"><i class="material-icons mr-0">&#xf964;</i></a></h5>
<canvas id="chart_div_avg_user"></canvas>
<?php endif;?>

<?php if (!empty($userWaitTimeByOperator)) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/avg_visitor_wait_time_by_operator.tpl.php'));?></h5>
<canvas id="chart_div_user_wait_time"></canvas>
<?php endif;?>

<?php if (!empty($subjectsStatistic)) : ?>
<hr>
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_subject.tpl.php'));?></h5>
<canvas id="chart_div_subjects_statistic"></canvas>
<?php endif; ?>

<?php if (!empty($userStats['thumbsup'])) : ?>
<canvas id="chart_div_upvotes_canvas"></canvas>
<?php endif; ?>

<?php if (!empty($userStats['thumbdown'])) : ?>
<canvas id="chart_div_downvotes_canvas"></canvas>
<?php endif; ?>

<?php else : ?>
<br/>
<div class="alert alert-info">
  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
</div>
<?php endif; ?>