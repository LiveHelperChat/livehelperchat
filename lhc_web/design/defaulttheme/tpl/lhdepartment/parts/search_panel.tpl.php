<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="mb-2" ng-non-bindable>

	<input type="hidden" name="doSearch" value="1">
	
	<div class="row">
	    <div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
			<input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($input->name)?>" />
		   </div>
		</div>
        <div class="col-md-2">
		   <div class="form-group">
			    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Alias');?></label>
               <div class="input-group input-group-sm">
                   <input type="text" class="form-control form-control-sm" name="alias" value="<?php echo htmlspecialchars($input->alias)?>" />
                   <button class="btn dropdown-toggle btn-outline-secondary border-secondary-control" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="material-icons">search</span></button>
                   <div class="dropdown-menu">
                       <label class="dropdown-item mb-0 ps-2"><input type="checkbox" name="empty_alias" <?php if ($input->empty_alias === true) : ?>checked="checked"<?php endif;?> value="true"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Empty')?></label>
                   </div>
               </div>
		   </div>
		</div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Identifier');?></label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm" name="identifier" value="<?php echo htmlspecialchars($input->identifier)?>" />
                    <button class="btn dropdown-toggle btn-outline-secondary border-secondary-control" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="material-icons">search</span></button>
                    <div class="dropdown-menu">
                        <label class="dropdown-item mb-0 ps-2"><input type="checkbox" name="empty_identifier" <?php if ($input->empty_identifier === true) : ?>checked="checked"<?php endif;?> value="true"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Empty')?></label>
                    </div>
                </div>
            </div>
		</div>
		<div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hidden');?></label>
			<select class="form-control form-control-sm" name="hidden">
                <option>All</option>
                <option value="1" <?php $input->hidden === 1 ? print 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?php $input->hidden === 0 ? print 'selected="selected"' : ''?>>No</option>
			</select>
		   </div>
		</div>
		<div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Visible only if online');?></label>
			<select class="form-control form-control-sm" name="visible_if_online">
                <option>All</option>
                <option value="1" <?php $input->visible_if_online === 1 ? print 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?php $input->visible_if_online === 0 ? print 'selected="selected"' : ''?>>No</option>
			</select>
		   </div>
		</div>
		<div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Disabled');?></label>
			<select class="form-control form-control-sm" name="disabled">
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