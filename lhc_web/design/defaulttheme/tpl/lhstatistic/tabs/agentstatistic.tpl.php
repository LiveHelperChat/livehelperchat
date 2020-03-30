<form action="" method="get" autocomplete="off">

<div class="row form-group">
    	
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

	<div class="col-md-3">
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

	<div class="col-md-3">
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

    <div class="col-md-3">
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

    <div class="col-md-12">
    	<div class="row">
    		<div class="col-md-1">
    			<input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
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
        $('.btn-block-department').makeDropdown();
	});
	
	$("#xmlagentstatistic").click(function(event) {
		event.preventDefault();
		var url = '<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/agentstatistic<?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input) : ''?>' + '?xmlagentstatistic=1';
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
        <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/part/agentstatistic/table_header.tpl.php'));?>
	</tr>
<?php foreach ($agentStatistic as $info) : ?>
	<tr>
        <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/part/agentstatistic/table_content.tpl.php'));?>
	</tr>
<?php endforeach; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/part/agentstatistic/table_average.tpl.php'));?>
</table>
<?php else : ?>
<br/>
<div class="alert alert-info">
  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
</div>
<?php endif; ?>
