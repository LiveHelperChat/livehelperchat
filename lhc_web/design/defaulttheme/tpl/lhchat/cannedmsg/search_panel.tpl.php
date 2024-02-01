<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Title');?></label>
                <input type="text" class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($input->title)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Message');?></label>
                <input type="text" class="form-control form-control-sm" name="message" value="<?php echo htmlspecialchars($input->message)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Fallback message');?></label>
                <input type="text" class="form-control form-control-sm" name="fmsg" value="<?php echo htmlspecialchars($input->fmsg)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_id[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_id,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'ajax'           => 'deps',
                    'list_function_params' => array_merge(['sort' => '`name` ASC','limit' => 20],erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'subject_id[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject'),
                    'selected_id'    => $input->subject_id,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function_params'  => array_merge((new erLhAbstractModelSubject())->getFilter(),['sort' => '`name` ASC']),
                    'list_function'  => 'erLhAbstractModelSubject::getList'
                )); ?>
            </div>
        </div>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_title.tpl.php')); ?>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $userTitle['user'];?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_id[]',
                    'optional_field' => $userTitle['user_select'],
                    'selected_id'    => $input->user_id,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name_official',
                    'ajax'           => 'users',
                    'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC','limit' => 50)),
                    'list_function'  => 'erLhcoreClassModelUser::getUserList',
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Usage frequency in the last 31 days');?></label>
                <select name="used_freq" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any times');?></option>
                    <option <?php if ($input->used_freq === 0) : ?>selected="selected"<?php endif;?> value="0" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Zero times');?></option>
                    <option value="1" <?php if ($input->used_freq === 1) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Once');?></option>
                    <option value="2" <?php if ($input->used_freq === 2) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','One or more');?></option>
                </select>
            </div>
        </div>

        <div class="col-md-2 pb-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Sort by');?></label>
            <select name="sortby" class="form-control form-control-sm">
                <option <?php if ($input->sortby == 'iddesc'|| $input->sortby == '') : ?>selected="selected"<?php endif; ?> value="iddesc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Newest first (default)');?></option>
                <option <?php if ($input->sortby == 'idasc') : ?>selected="selected"<?php endif; ?> value="idasc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Oldest first');?></option>
                <option <?php if ($input->sortby == 'lastupdatedesc') : ?>selected="selected"<?php endif; ?> value="lastupdatedesc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Recently updated first');?></option>
                <option <?php if ($input->sortby == 'lastupdateasc') : ?>selected="selected"<?php endif; ?> value="lastupdateasc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Recently updated last');?></option>
            </select>
        </div>



        <div class="col-md-4 pb-2 pt-4">

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
                <?php if ($pages->items_total > 0) : ?>
                    <a target="_blank" class="btn btn-outline-secondary btn-sm" href="<?php echo $pages->serverURL?>?export=1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','CSV');?></a>
                <?php endif; ?>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use_import'))  : ?>
                    <a target="_blank" class="btn btn-outline-secondary btn-sm" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'cannedmsg/import'})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?></a>
                <?php endif; ?>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','administratecannedmsg')) : ?>
                    <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>?quick_action=1'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">sync_alt</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions')?></button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>

</form>
