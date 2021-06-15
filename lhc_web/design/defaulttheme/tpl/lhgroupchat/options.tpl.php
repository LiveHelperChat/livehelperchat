<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Group chat options');?></h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','User group')?></label>
        <?php
            $params = array (
                'input_name'     => 'supervisor',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'selected_id'    => (isset($gc_options['supervisor']) ? $gc_options['supervisor'] : 0),
                'list_function'  => 'erLhcoreClassModelGroup::getList',
                'list_function_params'  => array_merge(array('limit' => '1000000'))
            );
            $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Not presented');
            echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
        </div>
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Operator in chat will see automatically members from this group to be invited into private support chat within chat.');?></p>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
