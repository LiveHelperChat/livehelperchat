<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form', 'Updated!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php')); ?>
<?php endif; ?>

<?php $fields = $object->getFields(); ?>

<div class="form-group">
    <label><?php echo $fields['name']['trans']; ?></label>
    <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object) ?>
</div>

<div class="form-group">
    <label><?php echo $fields['identifier']['trans']; ?> </label>
    <?php echo erLhcoreClassAbstract::renderInput('identifier', $fields['identifier'], $object) ?>
</div>

<p class="text-muted fs12">
    <ul>
        <li><small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/contentchunk", "Used as {chunk_implode__identifier__glue<__prefix__,><suffix__,>} in Replaceable variables"); ?></small></li>
        <li>E.g <small class="text-muted">{chunk_implode__function__,}</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/contentchunk", "Will implode all text chunks with identifier 'function' using ',' as glue."); ?></li>
        <li>E.g <small class="text-muted">{chunk_implode__function__,__suffix__,}</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/contentchunk", "Will implode all text chunks with identifier 'function' using ',' as glue. Will append ',' if at least one element is found."); ?></li>
        <li>E.g <small class="text-muted">{chunk_implode__function__,__prefix__,}</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/contentchunk", "Will implode all text chunks with identifier 'function' using ',' as glue. Will prepend ',' if at least one element is found."); ?></li>
        <li>E.g <small class="text-muted">{chunk_implode__function__,__suffix__,__prefix__,}</small> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/contentchunk", "Will implode all text chunks with identifier 'function' using ',' as glue. Will prepend and append ',' if at least one element is found."); ?></li>
    </ul>
</p>

<div class="form-group">
    <label><?php echo $fields['content']['trans']; ?></label>
    <?php echo erLhcoreClassAbstract::renderInput('content', $fields['content'], $object) ?>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('in_active', $fields['in_active'], $object) ?> <?php echo $fields['in_active']['trans']; ?></label>
</div>

<?php
$userDepartments = true;
if (!erLhcoreClassUser::instance()->hasAccessTo('lhabstract', 'see_global')) {
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter(erLhcoreClassUser::instance()->getUserID(), erLhcoreClassUser::instance()->cache_version);
}
$limitDepartments = $userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array();
?>

<div class="form-group">
    <div class="d-flex">
        <div class="d-inline pe-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg', 'Department'); ?></label>
        </div>
        <div class="d-inline pe-2">
            <?php
            $params = array(
                'input_name'           => 'cannedDepartmentGroup',
                'display_name'         => 'name',
                'css_class'            => 'form-control form-control-sm',
                'selected_id'          => 0,
                'list_function'        => 'erLhcoreClassModelDepartamentGroup::getList',
                'list_function_params' => array('limit' => false, 'sort' => '`name` ASC'),
                'optional_field'       => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit', 'Choose department group'),
            );
            echo erLhcoreClassRenderHelper::renderCombobox($params);
            ?>
        </div>
        <div class="d-inline">
            <div class="btn-group" role="group">
                <button type="button" id="check-by-department-group" class="btn btn-sm btn-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg', 'Check'); ?></button>
                <button type="button" id="uncheck-check-by-department-group" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg', 'Uncheck'); ?></button>
            </div>
        </div>
    </div>

    <div class="row" style="max-height: 500px; overflow: auto">
        <?php
        $params = array(
            'input_name'           => 'DepartmentID[]',
            'display_name'         => 'name',
            'css_class'            => 'form-control',
            'multiple'             => true,
            'wrap_prepend'         => '<div class="col-4">',
            'wrap_append'          => '</div>',
            'selected_id'          => $object->department_ids_front,
            'list_function'        => 'erLhcoreClassModelDepartament::getList',
            'list_function_params' => array_merge(array('sort' => '`name` ASC', 'limit' => false), $limitDepartments),
        );

        if (empty($limitDepartments)) {
            $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit', 'Any');
        }

        echo erLhcoreClassRenderHelper::renderCheckbox($params);
        ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#check-by-department-group,#uncheck-check-by-department-group').click(function () {
            var id = $(this).attr('id');
            $.getJSON(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/depbydepgroup?d=' + $('#id_cannedDepartmentGroup').val(), function (data) {
                data.items.forEach(function (item) {
                    $('#chk-DepartmentID-' + item).prop('checked', id == 'check-by-department-group');
                });
            });
        });
  
        ace.config.set('basePath', '<?php echo erLhcoreClassDesign::design('js/ace')?>');
        $('textarea[data-editor]').each(function() {
            var textarea = $(this);
            var mode = textarea.data('editor');
            var editDiv = $('<div>', {
                width: '100%',
                height: '200px',
                id: 'ace-'+textarea.attr('name')
            }).insertBefore(textarea);
            textarea.css('display', 'none');
            var editor = ace.edit(editDiv[0]);
            editor.renderer.setShowGutter(true);
            editor.getSession().setValue(textarea.val());
            editor.getSession().setMode("ace/mode/"+mode);
            editor.setOptions({
                autoScrollEditorIntoView: true,
                copyWithEmptySelection: true,
            });
            editor.setTheme("ace/theme/github");
            // copy back to textarea on form submit...
            textarea.closest('form').submit(function() {
                textarea.val(editor.getSession().getValue());
            })
        });

    });
</script>

<div class="btn-group btn-group-sm" role="group" aria-label="...">
    <input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>
