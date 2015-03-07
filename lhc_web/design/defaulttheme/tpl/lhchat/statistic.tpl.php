<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic');?></h1>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a onclick="redrawAllCharts(500)" href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic');?></a></li>
		<li role="presentation"><a href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','General');?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="panel1">
		
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
				},ts);
			};
			
			function drawChart() {
				
			  <?php if (!empty($userStats['thumbsup'])) : ?>			
			  var data = google.visualization.arrayToDataTable([
			    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>', '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Thumbs up');?>']
			    <?php foreach ($userStats['thumbsup'] as $data) : ?>
			    	<?php echo ',[\''.htmlspecialchars(erLhcoreClassModelUser::fetch($data['user_id'],true)->username,ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
			    <?php endforeach;?>
			  ]);			  
			  var view = new google.visualization.DataView(data);
		      view.setColumns([0,1]);              
			  var options = {
			    title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of Thumbs Up');?>',
			    hAxis: {titleTextStyle: {color: 'red'}},
		        width: '100%',
		        height: '100%'		      
			  };
			  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_upvotes'));
			  chartUp.draw(view, options);
			  <?php endif;?>

			  
			  <?php if (!empty($userStats['thumbdown'])) : ?>			  
			  var data = google.visualization.arrayToDataTable([
			    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Thumbs down');?>']
			    <?php foreach ($userStats['thumbdown'] as $data) : ?>
			    	<?php echo ',[\''.htmlspecialchars(erLhcoreClassModelUser::fetch($data['user_id'],true)->username,ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
			    <?php endforeach;?>
			  ]);			  
			  var view = new google.visualization.DataView(data);
		      view.setColumns([0,1]);              
			  var options = {
			    title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of Thumbs Down');?>',
			    hAxis: {titleTextStyle: {color: 'red'}},
		        width: '100%',
		        height: '100%'		      
			  };
			  var chartDown = new google.visualization.ColumnChart(document.getElementById('chart_div_downvotes'));
			  chartDown.draw(view, options);
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
				    title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of chats by country');?>',
				    hAxis: {titleTextStyle: {color: 'red'}},
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
				    	<?php echo ',[\''.htmlspecialchars(erLhcoreClassModelUser::fetch($data['user_id'],true)->username,ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
				    <?php endforeach;?>
				  ]);   
				  var options = {
				    title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of chats by user')?>',
				    hAxis: {titleTextStyle: {color: 'red'}},
			        width: '100%',
			        height: '100%'
				  };
				  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_user'));
				  chartUp.draw(data, options);	
				  <?php endif;?>						  
			};

			function drawChartUserAVGWaitTime() {	
				<?php if (!empty($userWaitTimeByOperator)) : ?>			
				  var data = google.visualization.arrayToDataTable([
				    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Wait time');?>']
				    <?php foreach ($userWaitTimeByOperator as $data) : ?>
				    	<?php echo ',[\''.htmlspecialchars(erLhcoreClassModelUser::fetch($data['user_id'],true)->username,ENT_QUOTES).'\','.$data['avg_wait_time'].']'?>
				    <?php endforeach;?>
				  ]);   
				  var options = {
				    title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','AVG visitor wait time by operator')?>',
				    hAxis: {titleTextStyle: {color: 'red'}},
			        width: '100%',
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
				    } else {
				    	$operator =erLhcoreClassModelUser::fetch($data['user_id'],true)->username;
				    }				    
				    ?>
				    	<?php echo ',[\''.htmlspecialchars($operator,ENT_QUOTES).'\','.$data['number_of_chats'].']'?>
				    <?php endforeach;?>
				  ]);	   
				  var options = {
				    title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of messages by user');?>',
				    hAxis: {titleTextStyle: {color: 'red'}},
			        width: '100%',
			        height: '100%'
				  };
				  var chartUp = new google.visualization.ColumnChart(document.getElementById('chart_div_user_msg'));
				  chartUp.draw(data, options);		
				<?php endif;?>					  
			};
						
			function drawChartPerMonth() {			
				  var data = google.visualization.arrayToDataTable([
				    ['<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Month');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Closed');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Active');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>']
				    <?php foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ?>
				    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['closed'].','.$data['active'].','.$data['operators'].','.$data['pending'].']'?>
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
				    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data.']'?>
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
				    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['chatinitdefault'].','.$data['chatinitproact'].']'?>
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
				    	<?php echo ',[\''.date('Y.m',$monthUnix).'\','.$data['msg_user'].','.$data['msg_operator'].','.$data['msg_system'].']'?>
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
			
			function drawChartWorkload() {			
				  var data = google.visualization.arrayToDataTable([
				    ['Hour', 'Chats']
				    <?php foreach ($numberOfChatsPerHour as $hour => $chatsNumber) : ?>
				    	<?php echo ',[\''.$hour.'\','.$chatsNumber.']'?>
				    <?php endforeach;?>
				  ]);					                  		  
				  var view = new google.visualization.DataView(data);			                    
				  var options = {
					title: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Number of chats per hour, average chat duration');?> <?php echo erLhcoreClassChat::formatSeconds(erLhcoreClassChatStatistic::getAverageChatduration())?>',
			        width: '100%',
			        height: '100%',
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
		
		<form action="" method="get">
		
		<div class="row form-group">
		
			<div class="col-md-3">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'user_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
	                    'selected_id'    => $input->user_id,
			            'css_class'      => 'form-control',
	                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
	            )); ?> 
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
 		 		 		 		
		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Hourly statistic');?></h5>
 		<hr>
 		<div id="chart_div_per_hour" style="width: 100%; height: 300px;"></div> 		 		
 		 		 		
 		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Country statistic');?></h5>
 		<hr>
 		<div id="chart_div_country" style="width: 100%; height: 300px;"></div>
 		 
 		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Users statisic');?></h5>
 		<hr>
 		<div id="chart_div_user" style="width: 100%; height: 300px;"></div>
 		<div id="chart_div_user_msg" style="width: 100%; height: 300px;"></div> 		
 		<div id="chart_div_user_wait_time" style="width: 100%; height: 300px;"></div> 		
 		<div class="row">
 			<div class="columns small-6"><div id="chart_div_upvotes" style="width: 100%; height: 300px;"></div></div>
 			<div class="columns small-6"><div id="chart_div_downvotes" style="width: 100%; height: 300px;"></div></div>
 		</div>
 		
 		
		</div>
		
		<div role="tabpanel" class="tab-pane" id="panel2">
		<div class="row">
			<div class="col-md-6">
				<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total statistic');?></h2>
				<table class="table">
					<thead>
						<tr>
							<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
							<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
						</tr>
					</thead>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount()?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total pending chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total active chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total closed chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chatbox chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (including visitors, system and operators messages)');?></td>
						<td><?php $totalMessagesCount = erLhcoreClassChat::getCount(array(),'lh_msg');
						echo $totalMessagesCount?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only visitors)');?></td>
						<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => 0)),'lh_msg');
						echo $totalVisitorsMessagesCount;?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only system messages)');?></td>
						<td><?php
						$systemMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => -1)),'lh_msg');
						echo $systemMessagesCount; ?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only operators)');?></td>
						<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last 24h statistic');?></h2>
				<table class="table">
					<thead>
						<tr>
							<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
							<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
						</tr>
					</thead>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600)))))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total pending chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total active chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total closed chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chatbox chats');?></td>
						<td><?php echo erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600))),'filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT)))?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (including visitors, system and operators messages)');?></td>
						<td><?php $totalMessagesCount = erLhcoreClassChat::getCount(array('filtergte' => array('time' => (time()-(24*3600)))),'lh_msg');
						echo $totalMessagesCount?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only visitors)');?></td>
						<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => 0),'filtergte' => array('time' => (time()-(24*3600)))),'lh_msg');
						echo $totalVisitorsMessagesCount;?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only system messages)');?></td>
						<td><?php
						$systemMessagesCount = erLhcoreClassChat::getCount(array('filter' => array('user_id' => -1),'filtergte' => array('time' => (time()-(24*3600)))),'lh_msg');
						echo $systemMessagesCount; ?></td>
					</tr>
					<tr>
						<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only operators)');?></td>
						<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
					</tr>
				</table>
			</div>
		</div>
		
		<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators last 24h statistic, top 100 by chats number');?></h2>
		<?php $operators = erLhcoreClassChatStatistic::getTopTodaysOperators(100); ?>
		
		<table class="table">
			<thead>
				<tr>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?></td>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages');?></td>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Votes');?></td>
					<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last activity');?></td>
				</tr>
			</thead>
			<?php foreach ($operators as $operator) : ?>
			<tr>
				<td><?php echo htmlspecialchars((string)$operator)?></td>
				<td><?php echo $operator->statistic_total_chats?></td>
				<td><?php echo $operator->statistic_total_messages?></td>
				<td>
				
				<i class="icon-thumbs-up up-voted"><?php echo $operator->statistic_upvotes?></i>
				<i class="icon-thumbs-down down-voted"><?php echo $operator->statistic_downvotes?></i>
				
				</td>
				<td><?php echo $operator->lastactivity_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','ago');?></td>
			</tr>
			<?php endforeach;?>
		</table>
		
		
		</div>
	</div>
</div>