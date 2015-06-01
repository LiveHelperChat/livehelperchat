<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  	google.load("visualization", "1", {packages:["corechart"]});
	
	var timeoutResize = null;			
	function redrawAllCharts(ts){
		clearTimeout(timeoutResize);
		setTimeout(function(){			
			drawChartPerMonth();
		},ts);
	};
			
	function drawChartPerMonth() {			
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Closed');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Active');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date($groupby,$monthUnix).'\','.$data['closed'].','.$data['active'].','.$data['operators'].','.$data['pending'].']'?>
		    <?php endforeach;?>
		  ]);					                  		  
		  var options = {
			title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats number by statuses');?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_per_month'));
		  chartUp.draw(data, options);
		  
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Time');?>']
		    <?php foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date($groupby,$monthUnix).'\','.$data.']'?>
		    <?php endforeach;?>
		  ]);   		  
		  var options = {
			title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','AVG wait time in seconds, max 10 mininutes');?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true
		  };
		  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_per_month_wait_time'));
		  chartUp.draw(data, options);

		  						  
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>', '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors initiated');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date($groupby,$monthUnix).'\','.$data['chatinitdefault'].','.$data['chatinitproact'].']'?>
		    <?php endforeach;?>
		  ]);		                    
		  var options = {
			title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive chats number vs visitors initiated');?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true
		  };
		  var chartProactive = new google.visualization.ColumnChart(document.getElementById('chart_type_div_per_month'));
		  chartProactive.draw(data, options);						  
  						  
		  var data = google.visualization.arrayToDataTable([
		    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>', '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','System');?>']
		    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
		    	<?php echo ',[\''.date($groupby,$monthUnix).'\','.$data['msg_user'].','.$data['msg_operator'].','.$data['msg_system'].']'?>
		    <?php endforeach;?>
		  ]);					                  		  
			                    
		  var options = {
			title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Messages types');?>',
	        width: '100%',
	        height: '100%',
	        isStacked: true
		  };
		  var chartMessages = new google.visualization.ColumnChart(document.getElementById('chart_type_div_msg_type'));
		  chartMessages.draw(data, options);						  
	}
	
	$(window).on("resize", function (event) {
		redrawAllCharts(100);
	});
	$( document ).ready(function() {
		redrawAllCharts(100);
	});
				
</script> 
		
<form action="" method="get">

<div class="row form-group">

	<div class="col-md-2">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'user_id',
				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                'selected_id'    => $input->user_id,
	            'css_class'      => 'form-control',
                'list_function'  => 'erLhcoreClassModelUser::getUserList'
        )); ?> 
    </div>   

    <div class="col-md-1">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group by');?></label>
	   <select name="groupby" class="form-control">
	       <option value="0" <?php $input->groupby == 0 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Month');?></option>
	       <option value="1" <?php $input->groupby == 1 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day');?></option>
	   </select>
	</div>

	<div class="col-md-3">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>

	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'department_id',
				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                'selected_id'    => $input->department_id,	
	            'css_class'      => 'form-control',			
                'list_function'  => 'erLhcoreClassModelDepartament::getList'
        )); ?> 
    </div>   
  
    <div class="col-md-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
		<div class="row">
			<div class="col-md-6">
				<input class="form-control" type="text" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
			</div>
			<div class="col-md-6">
				<input class="form-control" type="text" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
			</div>
		</div>
	</div>
	
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

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats statistic');?></h5>
<hr>
<div id="chart_div_per_month" style="width: 100%; height: 300px;"></div> 		 		
<div id="chart_type_div_per_month" style="width: 100%; height: 300px;"></div> 		
<div id="chart_type_div_msg_type" style="width: 100%; height: 300px;"></div>
<div id="chart_div_per_month_wait_time" style="width: 100%; height: 300px;"></div>

