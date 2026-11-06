<form action="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/AutoResponder" method="get">
    <div class="row">

        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'dep_id[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input_form->dep_id,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'ajax'           => 'deps',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 20], erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label><input type="checkbox" <?php echo $input_form->category == 1 ? print 'checked="checked"' : ''?> value="1" name="category" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Exclude personal responses');?></label>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <input type="submit" class="btn btn-secondary" value="Search" name="doSearch">
            </div>
        </div>

    </div>
</form>

<script>
$(function() {
    $('.btn-block-department').makeDropdown();
});
</script>