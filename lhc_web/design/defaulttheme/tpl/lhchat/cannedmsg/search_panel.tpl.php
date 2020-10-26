<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Title');?></label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($input->title)?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Message');?></label>
                <input type="text" class="form-control" name="message" value="<?php echo htmlspecialchars($input->message)?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Fallback message');?></label>
                <input type="text" class="form-control" name="fmsg" value="<?php echo htmlspecialchars($input->fmsg)?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'department_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
                    'selected_id'    => $input->department_id,
                    'css_class'      => 'form-control',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
        <?php if ($pages->items_total > 0) : ?>
            <a target="_blank" class="btn btn-secondary" href="<?php echo $pages->serverURL?>?export=1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','CSV');?></a>
        <?php endif; ?>
        
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use_import'))  : ?>
            <a target="_blank" class="btn btn-secondary text-white" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'cannedmsg/import'})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?></a>
        <?php endif; ?>
        
    </div>

</form>
