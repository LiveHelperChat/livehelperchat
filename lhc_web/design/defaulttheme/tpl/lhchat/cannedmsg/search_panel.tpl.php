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
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentFilter(),
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
                    'list_function_params'  => (new erLhAbstractModelSubject())->getFilter(),
                    'list_function'  => 'erLhAbstractModelSubject::getList'
                )); ?>
            </div>
        </div>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
        <?php if ($pages->items_total > 0) : ?>
            <a target="_blank" class="btn btn-secondary btn-sm" href="<?php echo $pages->serverURL?>?export=1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','CSV');?></a>
        <?php endif; ?>
        
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use_import'))  : ?>
            <a target="_blank" class="btn btn-secondary btn-sm text-white" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'cannedmsg/import'})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?></a>
        <?php endif; ?>
        
    </div>
    
    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>

</form>
