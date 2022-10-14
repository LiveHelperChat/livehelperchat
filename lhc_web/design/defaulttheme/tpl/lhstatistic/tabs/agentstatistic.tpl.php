<form action="" method="get" autocomplete="off" ng-non-bindable id="form-statistic-action">

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
			<div class="col-md-4">
			    <select name="timefrom_hours" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
			        <?php for ($i = 0; $i <= 23; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
			        <?php endfor;?>
			    </select>
			</div>
			<div class="col-md-4">
			    <select name="timefrom_minutes" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
			        <?php for ($i = 0; $i <= 59; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
			        <?php endfor;?>
			    </select>
			</div>
            <div class="col-md-4">
                <select name="timefrom_seconds" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_seconds) && $input->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
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
			<div class="col-md-4">
			    <select name="timeto_hours" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
			        <?php for ($i = 0; $i <= 23; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
			        <?php endfor;?>
			    </select>
			</div>
			<div class="col-md-4">
			    <select name="timeto_minutes" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
			        <?php for ($i = 0; $i <= 59; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
			        <?php endfor;?>
			    </select>
			</div>
            <div class="col-md-4">
                <select name="timeto_seconds" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                    <?php endfor;?>
                </select>
            </div>
	    </div>
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
               'list_function_params' => array_merge(array('sort' => '`name` ASC'),erLhcoreClassGroupUser::getConditionalUserFilter(false, true)),
               'list_function'  => 'erLhcoreClassModelGroup::getList'
            )); ?>
        </div>   
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'user_ids[]',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                'selected_id'    => $input->user_ids,
                'display_name'   => 'name_official',
                'css_class'      => 'form-control',
                'ajax'           => 'users',
                'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC','limit' => 50)),
                'list_function'  => 'erLhcoreClassModelUser::getUserList'
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
               'ajax'         => 'deps',
               'display_name'   => 'name',
               'list_function_params' => array_merge(['sort' => '`name` ASC','limit' => 20],erLhcoreClassUserDep::conditionalDepartmentFilter()),
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
               'list_function_params' => array_merge(['sort' => '`name` ASC'],erLhcoreClassUserDep::conditionalDepartmentGroupFilter()),
               'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
            )); ?>
        </div>   
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject');?></label>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'subject_ids[]',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose subjects for stats'),
                'selected_id'    => $input->subject_ids,
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params'  => array_merge((new erLhAbstractModelSubject())->getFilter(),['sort' => '`name` ASC']),
                'list_function'  => 'erLhAbstractModelSubject::getList'
            )); ?>
        </div>
    </div>

    <input type="hidden" name="doSearch" value="on" />

    <div class="col-12">
        <div class="btn-group mr-2" role="group" aria-label="...">
            <button type="submit" name="doSearch" class="btn btn-sm btn-primary" >
                <span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>
            </button>
            <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/agentstatistic"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
            <?php $tabStatistic = 'agentstatistic'; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhstatistic/report_button.tpl.php'));?>
            <?php if (!empty($agentStatistic)) : ?>
                    <a class="btn btn-outline-secondary btn-sm" id="xmlagentstatistic" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','All operators statistic will be downloaded')?>"><i class="material-icons mr-0">file_download</i></a>
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
