<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Group');?></label>
    <?php
    $params = array (
        'input_name'     => 'group_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control',
        'selected_id'    => $item->group_id,
        'list_function'  => 'erLhcoreClassModelGenericBotTrGroup::getList',
        'list_function_params'  => array('limit' => false)
    );
    $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose');
    echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Identifier');?></label>
    <input type="text" class="form-control" name="identifier"  value="<?php echo htmlspecialchars($item->identifier);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Translation');?></label>
    <textarea class="form-control form-control-sm" name="translation"><?php echo htmlspecialchars($item->translation);?></textarea>
</div>