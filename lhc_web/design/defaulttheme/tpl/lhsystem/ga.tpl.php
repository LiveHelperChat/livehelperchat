<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Events Tracking')?></h1>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="ga" <?php isset($ga_options['ga_enabled']) && ($ga_options['ga_enabled'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Enable Events Tracking')?></label><br/>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="ga_all" <?php isset($ga_options['ga_all']) && ($ga_options['ga_all'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track all departments')?></label>
        <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','If you do not choose any department from below we will track all departments.')?></small></p>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Enable tracking only for selected departments.')?></label>
        <?php
        $params = array (
            'input_name'     => 'ga_dep[]',
            'display_name'   => 'name',
            'multiple'       => true,
            'css_class'      => 'form-control form-control-sm',
            'selected_id'    => (isset($ga_options['ga_dep']) ? $ga_options['ga_dep'] : 0),
            'list_function'  => 'erLhcoreClassModelDepartament::getList',
        );
        echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhsystem/ga/event_form.tpl.php'));?>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
