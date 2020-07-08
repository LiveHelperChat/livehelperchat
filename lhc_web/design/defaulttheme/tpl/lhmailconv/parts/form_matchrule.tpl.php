<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Department');?></label>
    <?php
    $params = array (
        'input_name'     => 'dep_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control form-control-sm',
        'selected_id'    => $item->dep_id,
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params'  => array('limit' => '1000000'),
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose')
    );
    echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Condition');?></label>
    <textarea class="form-control form-control-sm" name="conditions"><?php echo htmlspecialchars($item->conditions)?></textarea>
</div>

<div class="form-group">
    <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Active');?></label>
</div>