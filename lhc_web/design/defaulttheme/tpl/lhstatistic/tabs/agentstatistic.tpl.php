<form action="" method="get">

<div class="row form-group">

    <div class="col-md-4">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
		<div class="row">
			<div class="col-md-6">
				<input class="form-control" type="text" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom == null ? date('Y-m-d',time()-7*24*3600) : $input->timefrom )?>" />
			</div>
			<div class="col-md-6">
				<input class="form-control" type="text" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
			</div>
		</div>
	</div>

	<div class="col-md-4">
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

	<div class="col-md-4">
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

    <div class="col-md-12">
    	<div class="row">
    		<div class="col-md-1">
    			<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
    		</div>
    		<?php if (!empty($agentStatistic)) : ?>
    		<div class="col-md-1">
    			<a id="xmlagentstatistic" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','All operators statistic will be downloaded')?>"><i class="material-icons mr-0">file_download</i></a>
    		</div>
    		<?php endif;?>
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

<?php if (!empty($agentStatistic)) : ?>
<table class="table statistic-table" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Agent');?></th>
		<th colspan="6"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats');?></th>
	</tr>
	<tr>
		<th></th>
		<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Number of chats');?></th>
		<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hours on chat');?></th>
		<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Ave number of chat per hour');?></th>
		<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average pick-up time');?></th>
		<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average chat length');?></th>
	</tr>
<?php foreach ($agentStatistic as $info) : ?>
	<tr>
		<td><?php echo $info->agentName; ?></td>
		<td><?php echo $info->numberOfChats; ?></td>
		<td><?php echo $info->totalHours; ?></td>
		<td><?php echo $info->aveNumber; ?></td>
		<td><?php echo $info->avgWaitTime; ?></td>
		<td><?php echo $info->avgChatLength; ?></td>
	</tr>
<?php endforeach; ?>
</table>
<?php else : ?>
<br/>
<div class="alert alert-info">
  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
</div>
<?php endif; ?>