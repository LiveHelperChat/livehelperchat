<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">

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
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Template HTML');?></label>
                <input type="text" class="form-control form-control-sm" name="template" value="<?php echo htmlspecialchars($input->template)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Template Plain text');?></label>
                <input type="text" class="form-control form-control-sm" name="template_plain" value="<?php echo htmlspecialchars($input->template_plain)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'dep_id[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->dep_id,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'ajax'           => 'deps',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 20],erLhcoreClassUserDep::conditionalDepartmentFilter()),
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
                    'list_function_params'  => array_merge((new erLhAbstractModelSubject())->getFilter(),['sort' => '`name` ASC', 'limit' => false]),
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

        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','use_import'))  : ?>
            <a target="_blank" class="btn btn-secondary btn-sm text-white" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'mailconv/importtemplate'})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?></a>
        <?php endif; ?>

    </div>

    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>

</form>
