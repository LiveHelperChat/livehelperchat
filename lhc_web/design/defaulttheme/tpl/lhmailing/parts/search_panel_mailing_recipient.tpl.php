<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mailing list');?></label>
        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
            'input_name'     => 'ml[]',
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose mailing list'),
            'selected_id'    => $input->ml,
            'css_class'      => 'form-control',
            'display_name'   => 'name',
            'list_function_params' => ['limit' => false],
            'list_function'  => 'erLhcoreClassModelMailconvMailingList::getList'
        )); ?>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
    </div>

    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>

</form>


