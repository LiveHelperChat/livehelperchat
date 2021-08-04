<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="mb-2" ng-non-bindable>

	<input type="hidden" name="doSearch" value="1">
	
	<div class="row">
	    <div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
			<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($input->name)?>" />
		   </div>
		</div>
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hidden');?></label>
			<select class="form-control" name="hidden">
                <option>All</option>
                <option value="1" <?php $input->hidden === 1 ? print 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?php $input->hidden === 0 ? print 'selected="selected"' : ''?>>No</option>
			</select>
		   </div>
		</div>
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Visible only if online');?></label>
			<select class="form-control" name="visible_if_online">
                <option>All</option>
                <option value="1" <?php $input->visible_if_online === 1 ? print 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?php $input->visible_if_online === 0 ? print 'selected="selected"' : ''?>>No</option>
			</select>
		   </div>
		</div>
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Disabled');?></label>
			<select class="form-control" name="disabled">
                <option>All</option>
                <option value="1" <?php $input->disabled === 1 ? print 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?php $input->disabled === 0 ? print 'selected="selected"' : ''?>>No</option>
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