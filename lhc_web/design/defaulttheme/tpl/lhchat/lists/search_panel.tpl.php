<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight">

	<input type="hidden" name="doSearch" value="1">


	<div class="row">
		<div class="columns large-2">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Nick');?></label>
			<input type="text" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" />
		</div>
		<div class="columns large-2">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
			<input type="text" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		</div>
		<div class="columns large-4">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
			<div class="row">
				<div class="columns large-6">
					<input type="text" name="timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
				</div>
				<div class="columns large-6">
					<input type="text" name="timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
				</div>
			</div>
		</div>
		<div class="columns large-4">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'department_id',
					'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
                    'selected_id'    => $input->department_id,				
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
            )); ?>            	
		</div>
	</div>
	
	<div>
		<input type="submit" name="doSearch" class="button radius small" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
	</div>

</form>