<form action="" method="get">

<div class="row form-group">

	<div class="col-md-3">
	   <div class="form-group">
        	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
        	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'user_id',
    				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                    'selected_id'    => $input->user_id,
    	            'css_class'      => 'form-control',
    	            'display_name'   => 'name_official',
                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
            )); ?>
        </div>
    </div>
    
	<div class="col-md-3">
	   <div class="form-group">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User group');?></label>
    	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'group_id',
				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
                'selected_id'    => $input->group_id,
	            'css_class'      => 'form-control',
	            'display_name'   => 'name',
                'list_function'  => 'erLhcoreClassModelGroup::getList'
        )); ?>
        </div>   
    </div>   

	<div class="col-md-3">
	    <div class="form-group">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
    	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'department_id',
    				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_id,	
    	            'css_class'      => 'form-control',			
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
            )); ?> 
        </div>   
    </div> 
      
	<div class="col-md-3">
	   <div class="form-group">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
    	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'department_group_id',
    				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
                    'selected_id'    => $input->department_group_id,	
    	            'css_class'      => 'form-control',			
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
            )); ?> 
        </div>   
    </div>   
  	
	<div class="col-md-3">
	  <div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
			<div class="row">
				<div class="col-md-12">
					<input type="text" class="form-control" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
				</div>							
			</div>
		</div>
	</div>
	
	<div class="col-md-3">
	  <div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
		<div class="row">				
			<div class="col-md-6">
			    <select name="timefrom_hours" class="form-control">
			        <option value="">Select hour</option>
			        <?php for ($i = 0; $i <= 23; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
			        <?php endfor;?>
			    </select>
			</div>
			<div class="col-md-6">
			    <select name="timefrom_minutes" class="form-control">
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
					<input type="text" class="form-control" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
				</div>							
			</div>
		</div>
	</div>
	
	<div class="col-md-3">
	  <div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
	    <div class="row">				
			<div class="col-md-6">
			    <select name="timeto_hours" class="form-control">
			        <option value="">Select hour</option>
			        <?php for ($i = 0; $i <= 23; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
			        <?php endfor;?>
			    </select>
			</div>
			<div class="col-md-6">
			    <select name="timeto_minutes" class="form-control">
			        <option value="">Select minute</option>
			        <?php for ($i = 0; $i <= 59; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
			        <?php endfor;?>
			    </select>
			</div>
	    </div>
	  </div>
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
	
	<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
	
	<script>
	$(function() {
		$('#id_timefrom,#id_timeto').fdatepicker({
			format: 'yyyy-mm-dd'
		});
	});
	</script>							
</form>

<?php if (isset($_GET['doSearch'])) : ?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  	google.load("visualization", "1", {packages:["corechart"]});
	
	var timeoutResize = null;			
	function redrawAllCharts(ts){
		clearTimeout(timeoutResize);
		setTimeout(function(){
			drawChart();
			drawChartCountry();
			drawChartUser();
			drawChartPerMonth();
			drawChartWorkload();
			drawChartUserMessages();
			drawChartUserAVGWaitTime();
			drawChartUserAverage();
            drawChartDepartmnent();
		},ts);
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
                        ctx.fillStyle = 'rgb(0, 0, 0)';
                        var fontSize = 12;
                        var fontStyle = 'normal';
                        var fontFamily = 'Arial';
                        ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                        // Just naively convert to string for now
                        var dataString = dataset.data[index].toString();
                        // Make sure alignment settings are correct
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        var padding = 5;
                        var position = element.tooltipPosition();
                        ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                    });
                }
            });
        }
    });


	function drawChart() {
	  <?php if (!empty($userStats['thumbsup'])) : ?>
        var barChartData = {
            labels: [<?php foreach ($userStats['thumbsup'] as $key => $data) : echo ($key > 0 ? ',' : ''),'\''.htmlspecialchars(erLhcoreClassModelUser::fetch($data['user_id'],true)->name_official,ENT_QUOTES).'\''; endforeach;?>],
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
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
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
                scales: {
                    xAxes: [{
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
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
	};
	
	function drawChartCountry() {	
		<?php if (!empty($countryStats)) : ?>						
		var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Country');?>', '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats');?>']
		    <?php foreach ($countryStats as $data) : ?>
		    	<?php echo ',[\''.htmlspecialchars($data['country_name'],ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
		    <?php endforeach;?>
		  ]);      
		var options = {
		    title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_country.tpl.php'));?>',
		    hAxis: {titleTextStyle: {color: 'red'},textStyle : {fontSize: 10}},
	        width: '100%',
	        height: '100%'		      
		  };
		var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_country'));
		chartUp.draw(data, options);	
		<?php endif;?>					  
	};
	
	function drawChartUser() {	
		<?php if (!empty($userChatsStats)) : ?>			
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats');?>']
		    <?php foreach ($userChatsStats as $data) : ?>
		    	<?php $obUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ',[\''.htmlspecialchars((is_object($obUser) ? $obUser->name_official : $data['user_id']),ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
		    <?php endforeach;?>
		  ]);   
		  var options = {		    
		    hAxis: {titleTextStyle: {color: 'red'},textStyle : {fontSize: 10}},
		    chartArea:{top:20},
	        width: '100%',
	        height: '100%'
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_user'));
		  chartUp.draw(data, options);	
		  <?php endif;?>						  
	};

	function drawChartDepartmnent() {
		<?php if (!empty($depChatsStats)) : ?>
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats');?>']
		    <?php foreach ($depChatsStats as $data) : ?>
		    	<?php $obUser = erLhcoreClassModelDepartament::fetch($data['dep_id'],true); echo ',[\''.htmlspecialchars((is_object($obUser) ? $obUser->name : $data['dep_id']),ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
		    <?php endforeach;?>
		  ]);
		  var options = {
		    hAxis: {titleTextStyle: {color: 'red'},textStyle : {fontSize: 10}},
		    chartArea:{top:20},
	        width: '100%',
	        height: '100%'
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_dep'));
		  chartUp.draw(data, options);
		  <?php endif;?>
	};
	
	function drawChartUserAverage() {	
		<?php if (!empty($userChatsAverageStats)) : ?>			
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average in seconds');?>']
		    <?php foreach ($userChatsAverageStats as $data) : ?>
		    	<?php $obUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ',[\''.htmlspecialchars((is_object($obUser) ? $obUser->name_official : $data['user_id']),ENT_QUOTES).'\','.$data['avg_chat_duration'].']'?>
		    <?php endforeach;?>
		  ]);   
		  var options = {		    
		    hAxis: {titleTextStyle: {color: 'red'},textStyle : {fontSize: 10}},
		    chartArea:{top:20},
	        width: '100%',
	        height: '100%'
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_avg_user'));
		  chartUp.draw(data, options);	
		  <?php endif;?>						  
	};

	function drawChartUserAVGWaitTime() {	
		<?php if (!empty($userWaitTimeByOperator)) : ?>			
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Wait time');?>']
		    <?php foreach ($userWaitTimeByOperator as $data) : ?>
		    	<?php $obUser = erLhcoreClassModelUser::fetch($data['user_id'],true); echo ',[\''.htmlspecialchars((is_object($obUser) ? $obUser->name_official : $data['user_id']),ENT_QUOTES).'\','.$data['avg_wait_time'].']'?>
		    <?php endforeach;?>
		  ]);   
		  var options = {
		    hAxis: {titleTextStyle: {color: 'red'},textStyle : {fontSize: 10}},
	        width: '100%',
	        chartArea:{top:20},
	        height: '100%'
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_user_wait_time'));
		  chartUp.draw(data, options);	
		  <?php endif;?>						  
	};
	
	function drawChartUserMessages() {
		<?php if (!empty($numberOfMsgByUser)) : ?>			
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Messages');?>']
		    <?php foreach ($numberOfMsgByUser as $data) : 				    
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
		    }				    
		    ?>
		    <?php echo ',[\''.htmlspecialchars($operator,ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
		    <?php endforeach;?>
		  ]);	   
		  var options = {
		    hAxis: {titleTextStyle: {color: 'red'},textStyle : {fontSize: 10}},
	        width: '100%',
	        chartArea:{top:20},
	        height: '100%'
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_user_msg'));
		  chartUp.draw(data, options);		
		<?php endif;?>					  
	};
				
	function drawChartPerMonth() {	

		  // Chats number by statuses	
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Closed');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Active');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['closed'].','.$data['active'].','.$data['operators'].','.$data['pending'].']'?>
		    <?php endforeach;?>
		  ]);					                  		  
		  var options = {
			title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chats_number_by_statuses.tpl.php'));?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true,
            hAxis : {textStyle:{fontSize: 10}}
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_per_month'));
		  chartUp.draw(data, options);

		  
		  // Chats number by unanswered chats
          var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['unanswered'].']'?>
		    <?php endforeach;?>
		  ]);   		  
		  var options = {
			title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats_numbers.tpl.php'));?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true,
              hAxis : {
                  textStyle : {
                      fontSize: 10 // or the number you want
                  }
              }
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_per_month_unanswered'));
		  chartUp.draw(data, options);

		  
		  // AVG Wait time
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Time');?>']
		    <?php foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data.']'?>
		    <?php endforeach;?>
		  ]);   		  
		  var options = {
			title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/avg_wait_time_in_seconds_max_10_mininutes.tpl.php'));?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true,
              hAxis : {
                  textStyle : {
                      fontSize: 10 // or the number you want
                  }
              }
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_per_month_wait_time'));
		  chartUp.draw(data, options);

		  						  
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>', '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors initiated');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['chatinitdefault'].','.$data['chatinitproact'].']'?>
		    <?php endforeach;?>
		  ]);		                    
		  var options = {
			title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/proactive_chats_number_vs_visitors_initiated.tpl.php'));?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true,
            hAxis : {
                textStyle : {
                    fontSize: 10 // or the number you want
                }
            }
		  };
		  var chartProactive = new google.visualization.ColumnChart(document.getElementById('chart_type_div_per_month'));
		  chartProactive.draw(data, options);						  
  						  
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>', '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','System');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['msg_user'].','.$data['msg_operator'].','.$data['msg_system'].']'?>
		    <?php endforeach;?>
		  ]);					                  		  
			                    
		  var options = {
			title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/messages_types.tpl.php'));?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true,
            hAxis : {
                 textStyle : {
                    fontSize: 10 // or the number you want
                }
            }
		  };
		  var chartMessages = new google.visualization.ColumnChart(document.getElementById('chart_type_div_msg_type'));
		  chartMessages.draw(data, options);						  
	}
	
	function drawChartWorkload() {			
		  var data = google.visualization.arrayToDataTable([
		    ['Hour', 'Chats']
		    <?php foreach ($numberOfChatsPerHour as $hour => $chatsNumber) : ?>
		    	<?php echo ',[\''.$hour.'\','.$chatsNumber.']'?>
		    <?php endforeach;?>
		  ]);					                  		  
		  var view = new google.visualization.DataView(data);			                    
		  var options = {
			title: '<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_per_hour_average_chat_duration.tpl.php'));?> <?php echo $averageChatTime != null ? erLhcoreClassChat::formatSeconds($averageChatTime) : '(-)';?>',
	        width: '100%',
	        height: '100%',
            hAxis : {
                textStyle : {
                    fontSize: 10 // or the number you want
                }
            },
	        isStacked: true
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_per_hour'));
		  chartUp.draw(view, options);				  						  
	}
	
	$(window).on("resize", function (event) {
		redrawAllCharts(100);
	});

	$( document ).ready(function() {
		redrawAllCharts(100);
	});
				
</script> 

<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chats_statistic.tpl.php'));?></h5>
<hr>
<div id="chart_div_per_month" style="width: 100%; height: 300px;"></div> 		 		
<div id="chart_type_div_per_month" style="width: 100%; height: 300px;"></div> 		
<div id="chart_type_div_msg_type" style="width: 100%; height: 300px;"></div>
<div id="chart_div_per_month_wait_time" style="width: 100%; height: 300px;"></div>
<div id="chart_div_per_month_unanswered" style="width: 100%; height: 300px;"></div>
 		 		 		
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/hourly_statistic.tpl.php'));?></h5>
<hr>
<div id="chart_div_per_hour" style="width: 100%; height: 300px;"></div> 		 		
 		 		
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/country_statistic.tpl.php'));?></h5>
<hr>
<div id="chart_div_country" style="width: 100%; height: 300px;"></div>
 
<h5><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/users_statisic.tpl.php'));?></h5>
<hr>

<?php if (!empty($userChatsStats)) : ?>	
<div class="pl20"><strong><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_user.tpl.php'));?></strong></div>
<div id="chart_div_user" style="width: 100%; height: 300px;"></div>
<?php endif;?>

<?php if (!empty($depChatsStats)) : ?>
<div class="pl20"><strong><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_chats_by_dep.tpl.php'));?></strong></div>
<div id="chart_div_dep" style="width: 100%; height: 300px;"></div>
<?php endif;?>

<?php if (!empty($numberOfMsgByUser)) : ?>	
<div class="pl20"><strong><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/number_of_messages_by_user.tpl.php'));?></strong></div>
<div id="chart_div_user_msg" style="width: 100%; height: 300px;"></div> 		
<?php endif;?>

<?php if (!empty($userChatsAverageStats)) : ?>
<div class="pl20"><strong><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/average_chat_duration_by_user.tpl.php'));?></strong>
    <a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?><?php echo $urlappend?>?xmlavguser=1" target="_blank" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','All operators statistic will be downloaded')?>"><i class="material-icons mr-0">file_download</i></a>
</div>
<div id="chart_div_avg_user" style="width: 100%; height: 300px;"></div> 
<?php endif;?>

<?php if (!empty($userWaitTimeByOperator)) : ?>	
<div class="pl20"><strong><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/avg_visitor_wait_time_by_operator.tpl.php'));?></strong></div>
<div id="chart_div_user_wait_time" style="width: 100%; height: 300px;"></div> 	
<?php endif;?>

<canvas id="chart_div_upvotes_canvas"></canvas>

<canvas id="chart_div_downvotes_canvas"></canvas>

<?php else : ?>
<br/>
<div class="alert alert-info">
  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
</div>
<?php endif; ?>