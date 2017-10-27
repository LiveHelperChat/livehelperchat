<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight">
	<input type="hidden" name="doSearch" value="1">
	<div class="row">		
		<div class="col-md-6">
		    <div class="form-group">
    			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
    			<div class="row">
    				<div class="col-md-6">
    					<input type="text" class="form-control" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
    				</div>
    				<div class="col-md-6">
    					<input type="text" class="form-control" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
    				</div>
    			</div>
			</div>
		</div>
		<div class="col-md-6">
		  <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
				<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'department_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
	                    'selected_id'    => $input->department_id,
				        'css_class'      => 'form-control',				
	                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
	            )); ?>            	 
		   </div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'user_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
	                    'selected_id'    => $input->user_id,
			            'css_class'      => 'form-control',
	                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
	            )); ?>            	
		  </div>
		</div>
	</div>
	<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>
	
	<div class="row">
	<?php for ($i = 0; $i < 16; $i++) : ?>    
    	<?php foreach ($sortOptions as $keyOption => $sortOption) : ?>    	   		    
    		<?php if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'} == 1) : ?>
    			<?php if ($sortOption['type'] == 'stars') : ?>
    			<div class="col-xs-3">
    				<div class="form-group">
				    	<label><?php echo htmlspecialchars($survey->{$sortOption['field'] . '_title'});?></label>
				    	<select name="<?php echo $sortOption['field']?>[]" class="form-control" multiple="multiple" size="5">
				    		<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any')?></option>
				        <?php for ($n = 1; $n <= $survey->{$sortOption['field']}; $n++) : ?>
				        	<option value="<?php echo $n?>" <?php if (is_array($input->{$sortOption['field']}) && in_array($n, $input->{$sortOption['field']})) : ?>selected="selected"<?php endif;?>><?php echo $n?> stars</option>
				        <?php endfor;?>      
				        </select>
				    </div>
				</div>
    			<?php endif;?>
    			
    			<?php if ($sortOption['type'] == 'question_options') : ?>
    			<div class="col-xs-3">
    				<div class="form-group">
				    	<label><?php echo htmlspecialchars($survey->{$sortOption['field']});?></label>				    	
				    	<select name="<?php echo $sortOption['field']?>" class="form-control">
				    		<option value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any')?></option>
				        	<?php foreach ($survey->{$sortOption['field'] . '_items_front'} as $key => $item) : ?>
				        		<option value="<?php echo $key+1?>" <?php if ($input->{$sortOption['field']} == ($key+1)) : ?>selected="selected"<?php endif;?>><?php echo erLhcoreClassSurveyValidator::parseAnswerPlain($item['option'])?></option>
				        	<?php endforeach;?>      
				        </select>				        
				    </div>
				</div>
    			<?php endif;?>
    			
			<?php endif;?>
		<?php endforeach;?>
	<?php endfor;?>
	</div>
	
	
	<div class="row">
	   <div class="col-xs-6">
	       <label><input ng-model="group_results" ng-init="group_results = <?php ($input->group_results == true) ? print 'true' : 'false' ?>" type="checkbox" name="group_results" value="on" <?php if ($input->group_results == true) : ?>checked="checked"<?php endif;?>/> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group results by operator');?></label>
	   </div>
	   <div class="col-xs-6">
    	   <div class="form-group" ng-show="group_results">
    	      <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Minimum chats');?></label>
    	      <input class="form-control" type="text" name="minimum_chats" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Optional')?>" value="<?php echo htmlspecialchars($input->minimum_chats)?>" />
    	   </div>
	   </div>
	</div>
	<div class="btn-group" role="group" aria-label="...">
		<input type="submit"  name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
		
		<?php if ($pages->items_total > 0) : ?>
		<a target="_blank" class="btn btn-default" href="<?php echo $pages->serverURL?>/(print)/1"><i class="material-icons">print</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Print');?></a>
		
		<?php if ($input->group_results == true) : ?>
		  <a target="_blank" class="btn btn-default" href="<?php echo $pages->serverURL?>/(xls)/1"><i class="material-icons">&#xE2C4;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','XLS');?></a>
		<?php endif;?>
		
		<?php endif; ?>
	</div>	
</form>

<script>
$(function() {
	$('#id_timefrom,#id_timeto').fdatepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>