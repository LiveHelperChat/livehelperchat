<div class="form-group mh275 change-dep-action overflow-visible">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
    <?php
    $userDepartments = true;
    if (!erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowtransfertoanydep')) {
        $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter(erLhcoreClassUser::instance()->getUserID(), erLhcoreClassUser::instance()->cache_version);
    }
    ?>

    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
        'input_name'     => 'new_dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
        'selected_id'    => $chat->dep_id,
        'type'           => 'radio',
        'data_prop'      => 'data-limit="1"',
        'css_class'      => 'form-control',
        'display_name'   => 'name',
        'show_optional'  => true,
        'list_function_params'  => array_merge(array('sort' => '`name` ASC', 'limit' => false), ($userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array())),
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
    )); ?>

    <script>
        $('.change-dep-action .btn-block-department').makeDropdown();
    </script>
</div>