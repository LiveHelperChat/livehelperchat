<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Name');?></label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
    <?php
    $params = array (
        'input_name'     => 'dep_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control form-control-sm',
        'selected_id'    => $item->dep_id,
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params'  => array_merge(array('limit' => '1000000'))
    );

    $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Any');

    echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>


<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Template');?></label>
    <textarea class="form-control form-control-sm" name="template"><?php echo htmlspecialchars($item->template)?></textarea>
</div>