<form action="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ContentChunk" method="get">
    <div class="row">

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
                <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars((string)$input_form->name)?>" />
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/contentchunk','Identifier');?></label>
                <input type="text" class="form-control form-control-sm" name="object_identifier" value="<?php echo htmlspecialchars((string)$input_form->object_identifier)?>" />
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'dep_id[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input_form->dep_id,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'ajax'           => 'deps',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 20], erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>

        <div class="col-12">
            <div class="mb-3">
                <input type="submit" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" name="doSearch">
                <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ContentChunk"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
            </div>
        </div>

    </div>
</form>

<script>
$(function() {
    $('.btn-block-department').makeDropdown();
});
</script>
