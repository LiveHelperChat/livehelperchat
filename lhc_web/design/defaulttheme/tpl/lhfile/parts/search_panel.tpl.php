<form action="<?php echo $input->form_action?>" method="get" >

	<input type="hidden" name="doSearch" value="1">
							
	<div class="row">		
		<div class="col-xs-6">
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'user_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
	                    'selected_id'    => $input->user_id,
			             'css_class' => 'form-control',				
	                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
	            )); ?>            	
		</div>
		<div class="col-xs-6">
			<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" /> 
			          	
		</div>	
	</div>	
</form>