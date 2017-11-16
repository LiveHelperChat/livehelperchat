<form action="" method="get">

<div class="row form-group">
    <div class="col-md-2">
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
	
	<div class="col-md-2">
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
	
	<div class="col-md-2">
        <div class="form-group">
		    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Include hours (from, to)');?></label>		    
			<div class="row">
				<div class="col-md-6">
					<select name="timefrom_include_hours" class="form-control">
    			        <option value="">Select hour</option>
    			        <?php for ($i = 0; $i <= 23; $i++) : ?>
    			            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_include_hours) && $input->timefrom_include_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
    			        <?php endfor;?>
    			    </select>
				</div>
				<div class="col-md-6">
					<select name="timeto_include_hours" class="form-control">
    			        <option value="">Select hour</option>
    			        <?php for ($i = 0; $i <= 23; $i++) : ?>
    			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_include_hours) && $input->timeto_include_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
    			        <?php endfor;?>
    			    </select>
				</div>
			</div>			
        </div>
	</div>
	
	<div class="col-md-3">
	   <div class="form-group">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
               'input_name'     => 'user_id',
               'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
               'selected_id'    => $input->user_id,
               'css_class'      => 'form-control',
               'display_name' => 'name_official',
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
    
    <div class="col-md-12">
    	<div class="row">
    		<div class="col-md-1">
    			<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
    		</div>
    	</div>		
	</div>
	
</div>

<script>
	$(function() {
		$('#id_timefrom,#id_timeto').fdatepicker({
			format: 'yyyy-mm-dd'
		});
	});
	
	$("#xmlagentstatistic").click(function(event) {
		event.preventDefault();
		var url = '<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/agentstatistic/<?php echo isset($urlappend) ? $urlappend : ''?>' + '?xmlagentstatistic=1';
		
		if ($("#id_timefrom").val() != '') {
			url = url + '&timefrom=' + $("#id_timefrom").val();
		}
		
		if ($("#id_timeto").val() != '') {
			url = url + '&timeto=' + $("#id_timeto").val();
		}
		
		if (($("#id_timefrom").val() != '') || ($("#id_timeto").val() != '')) {
			url = url + '&doSearch=Search';
		}
		window.open(url,'_blank');
	})
</script>
							
</form>

<?php if (!empty($performanceStatistic['rows'])) : ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Avg. Wait Time');?></a></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats Started');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Abandoned Chats');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','% of chats');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Abandoned at time %');?></th>
             </tr>
        </thead>
        <tbody>
            <?php foreach ($performanceStatistic['rows'] as $stat) : ?>
                <tr>
                    <td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(wait_time_from)/<?php echo $stat['from']-1?><?php $stat['to'] !== false ? print '/(wait_time_till)/' . $stat['to'] : ''?>/<?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>"><?php echo htmlspecialchars($stat['tt'])?></a></td>
                    <td><?php echo $stat['started']?></td>
                    <td><?php echo $stat['abandoned']?></td>
                    <td><?php echo $performanceStatistic['total_chats'] > 0 ? round($stat['started']/$performanceStatistic['total_chats'],4)*100 : 0?> %</td>
                    <td><?php echo $stat['started'] > 0 ? round($stat['abandoned']/$stat['started'],4)*100 : 0?> %</td>
                </tr>
            <?php endforeach; ?>
                <tr>
                    <td><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total');?></b></td>
                    <td><b><?php echo $performanceStatistic['total_chats']?></b></td>
                    <td><b><?php echo $performanceStatistic['total_aband_chats']?></b></td>
                    <td></td>
                    <td></td>
                </tr>
        </tbody>
    </table>
<?php endif; ?>