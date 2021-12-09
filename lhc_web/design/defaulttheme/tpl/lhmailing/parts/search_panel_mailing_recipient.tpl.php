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
        <button type="button" class="btn btn-outline-secondary btn-sm"  onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/newmailingrecipient')?><?php if (!empty($input->ml)) : ?>/(ml)/<?php echo implode('/',$input->ml)?><?php endif;?>'})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></button>
    </div>

    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>

    <div role="alert" class="alert alert-info alert-dismissible hide m-3" id="list-update-import">
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','This list was updated. Please');?>&nbsp;<a href="?refresh=<?php echo time()?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','refresh');?>.</a>
    </div>

</form>


