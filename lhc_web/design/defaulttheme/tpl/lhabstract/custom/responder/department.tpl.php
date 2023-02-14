<?php
$showAnyDepartment = erLhcoreClassUser::instance()->hasAccessTo('lhautoresponder','see_global');
$userDepartments = true;
if (!erLhcoreClassUser::instance()->hasAccessTo('lhautoresponder','exploreautoresponder_all')) {
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter( erLhcoreClassUser::instance()->getUserID(),  erLhcoreClassUser::instance()->cache_version);
}
$limitDepartments = $userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array();
?>
<div class="form-group">

    <div class="d-flex">
        <div class="d-inline pe-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
        </div>
        <div class="d-inline pe-2">
            <?php
            $params = array (
                'input_name'     => 'cannedDepartmentGroup',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'selected_id'    => 0,
                'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList',
                'list_function_params'  => array('limit' => false,'sort' => '`name` ASC'),
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose department group')
            );
            echo erLhcoreClassRenderHelper::renderCombobox($params);
            ?>
        </div>
        <div class="d-inline">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" id="check-by-department-group" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Check all departments from selected department group');?>" class="btn btn-sm btn-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Check');?></button>
                <button type="button" id="uncheck-check-by-department-group" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Uncheck all departments from selected department group');?>" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Uncheck');?></button>
            </div>
        </div>
    </div>

    <div class="row" style="max-height: 500px; overflow: auto">
        <?php
        $params = array (
            'input_name'     => 'DepartmentID[]',
            'display_name'   => 'name',
            'css_class'      => 'form-control',
            'multiple'       => true,
            'wrap_prepend'   => '<div class="col-4">',
            'wrap_append'    => '</div>',
            'selected_id'    => $object->department_ids_front,
            'list_function'  => 'erLhcoreClassModelDepartament::getList',
            'list_function_params'  => array_merge(array('sort' => '`name` ASC', 'limit' => false), $limitDepartments)
        );

        if (empty($limitDepartments) || (isset($showAnyDepartment) && $showAnyDepartment == true)) {
            $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any');
        }

        echo erLhcoreClassRenderHelper::renderCheckbox( $params ); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('#check-by-department-group,#uncheck-check-by-department-group').click(function(){
            var id = $(this).attr('id');
            $.getJSON(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/depbydepgroup?d='+$('#id_cannedDepartmentGroup').val(), function(data) {
                data.items.forEach( function(item) {
                    $('#chk-DepartmentID-'+item).prop('checked', id == 'check-by-department-group');
                })
            });
        });
    });
</script>