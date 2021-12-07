<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','E-mail');?>*</label>
    <input type="text" maxlength="50" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($item->email)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','This recipient is a member of these mailing lists');?></label>
    <div class="row" style="max-height: 500px; overflow: auto">
        <?php
            $params = array (
                'input_name'     => 'ml[]',
                'display_name'   => 'name',
                'css_class'      => 'form-control',
                'multiple'       => true,
                'wrap_prepend'   => '<div class="col-4">',
                'wrap_append'    => '</div>',
                'selected_id'    => $item->ml_ids_front,
                'list_function'  => 'erLhcoreClassModelMailconvMailingList::getList',
                'list_function_params'  => array('sort' => 'name ASC, id ASC', 'limit' => false)
            );
            echo erLhcoreClassRenderHelper::renderCheckbox( $params );
        ?>
    </div>
</div>

<hr>

<div class="form-group">
    <label><input type="checkbox" name="disabled" value="on" <?php $item->disabled == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Disabled');?></label>
</div>