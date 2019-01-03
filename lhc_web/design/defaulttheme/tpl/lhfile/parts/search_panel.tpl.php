<form action="<?php echo $input->form_action?>" method="get" >

	<input type="hidden" name="doSearch" value="1">
							
	<div class="row">		
		<div class="col-3">

            <div class="form-group">
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'user_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
	                    'selected_id'    => $input->user_id,
			            'css_class' => 'form-control',
	                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
	            )); ?>
            </div>

            <label><input type="checkbox" name="visitor" <?php if ($input->visitor === 0) : ?>checked="checked"<?php endif; ?> value="0">Only visitor files</label>

            <label><input type="checkbox" name="persistent" <?php if ($input->persistent == 1) : ?>checked="checked"<?php endif; ?> value="1">Only persistent</label>

		</div>
		<div class="col-6">
			<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" /> 
			          	
		</div>	
	</div>	
</form>