<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="mb-2" ng-non-bindable>

	<input type="hidden" name="doSearch" value="1">

	<div class="row">
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Username');?></label>
			<input type="text" class="form-control form-control-sm" name="username" value="<?php echo htmlspecialchars($input->username)?>" />
		   </div>
		</div>
        <div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Nickname');?></label>
			<input type="text" class="form-control form-control-sm" name="chat_nickname" value="<?php echo htmlspecialchars($input->chat_nickname)?>" />
		   </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
			<input type="text" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		  </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
			<input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($input->name)?>" />
		  </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Surname');?></label>
			<input type="text" class="form-control form-control-sm" name="surname" value="<?php echo htmlspecialchars($input->surname)?>" />
		  </div>
		</div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
                    'selected_id'    => $input->group_ids,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function_params' => [],
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Account status');?></label>
                <select name="disabled" class="form-control form-control-sm">
                    <option value="">Active & Deactivated</option>
                    <option value="0" <?php if ($input->disabled === 0) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active');?></option>
                    <option value="1" <?php if ($input->disabled === 1) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Deactivated');?></option>
                </select>
            </div>
        </div>
        
    </div>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" name="doSearch" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
        <?php if (isset($pages) && $pages->items_total > 0) : ?>
            <a target="_blank" class="btn btn-outline-secondary btn-sm" href="<?php echo $pages->serverURL?>/(export)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export CSV');?> (<?php echo $pages->items_total?>)</a>
        <?php endif; ?>
	</div>

</form>
<script>
    $(function() {
        $('.btn-block-department').makeDropdown();
    });
</script>