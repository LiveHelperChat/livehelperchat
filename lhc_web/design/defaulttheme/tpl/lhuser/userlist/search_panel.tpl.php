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
			<input type="text" class="form-control form-control-sm" name="chat_nickname" value="<?php echo htmlspecialchars((string)$input->chat_nickname)?>" />
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
                    'list_function_params' => array_merge(array('sort' => '`name` ASC'),erLhcoreClassGroupUser::getConditionalUserFilter(false, true)),
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Account status');?></label>
                <select name="disabled" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active & Deactivated');?></option>
                    <option value="0" <?php if ($input->disabled === 0) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active');?></option>
                    <option value="1" <?php if ($input->disabled === 1) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Deactivated');?></option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User language');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_languages[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
                    'selected_id'    => $input->user_languages,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'dialect_name',
                    'attr_id'        => 'lang_code',
                    'no_selector'    => true,
                    'list_function_params' => array('sort' => '`lang_name` ASC'),
                    'list_function'  => 'erLhcoreClassModelSpeechLanguageDialect::getList'
                )); ?>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Login date range from');?></label>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" autocomplete="off" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
                        <div class="row">
                            <div class="col-md-6">
                                <select name="timefrom_hours" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                                    <?php for ($i = 0; $i <= 23; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="timefrom_minutes" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Login date range to');?></label>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" autocomplete="off" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
                        <div class="row">
                            <div class="col-md-6">
                                <select name="timeto_hours" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                                    <?php for ($i = 0; $i <= 23; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="timeto_minutes" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" name="doSearch" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />

        <?php if (isset($pages) && $pages->items_total > 0) : ?>

            <?php if (!isset($searchPanelOptions['hide_export_csv'])) : ?>
                <a target="_blank" class="btn btn-outline-secondary btn-sm" href="<?php echo $pages->serverURL?>/(export)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export CSV');?> (<?php echo $pages->items_total?>)</a>
            <?php endif; ?>

            <?php if (!isset($searchPanelOptions['hide_quick_actions']) && erLhcoreClassUser::instance()->hasAccessTo('lhuser','quick_actions')) : ?>
                <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/quick_actions?'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">sync_alt</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions')?></button>
            <?php endif; ?>
        
        <?php endif; ?>

        <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('user/list')?>"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
	</div>

</form>
<script>
    $(function() {
        $('.btn-block-department').makeDropdown();
        $('#id_timefrom,#id_timeto').fdatepicker({
            format: 'yyyy-mm-dd'
        });
    });
</script>