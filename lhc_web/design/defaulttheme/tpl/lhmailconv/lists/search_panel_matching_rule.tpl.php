<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="mb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mailbox');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'mailbox_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose mailbox'),
                    'selected_id'    => $input->mailbox_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'mail',
                    'list_function_params' => ['limit' => false, 'sort' => '`mail` ASC'],
                    'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_ids,
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
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
                    'selected_id'    => $input->department_group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => false],erLhcoreClassUserDep::conditionalDepartmentGroupFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','From name');?></label>
                <input type="text" class="form-control form-control-sm" name="from_name" value="<?php echo htmlspecialchars($input->from_name)?>" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','From mail');?></label>
                <input type="text" class="form-control form-control-sm" name="from_mail" value="<?php echo htmlspecialchars($input->from_mail)?>" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject contains');?></label>
                <input type="text" class="form-control form-control-sm" name="subject_contains" value="<?php echo htmlspecialchars($input->subject_contains)?>" />
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="...">
                <button class="btn btn-primary btn-sm" type="submit" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?></button>
            </div>
        </div>
    </div>

</form>

<script>
    $(function() {
        $('.btn-block-department').makeDropdown();
    });
</script>