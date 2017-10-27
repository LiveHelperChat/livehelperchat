<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Import users')?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Updated!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($imported)) : ?>
    <?php
        $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Updated') . ' - ' . $imported['updated'] . '<br/>' . erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Created') . ' - ' . $imported['created'];
    ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div>
    <form action="<?php echo erLhcoreClassDesign::baseurl('user/import')?>" method="post" enctype="multipart/form-data">

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        <div class="row">
            <div class="col-xs-3">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','CSV Separator')?></label>
                    <select class="form-control" name="CSVSeparator">
                        <option value="," <?php if (isset($importSettings['csv_separator']) && $importSettings['csv_separator'] == ',') : ?>selected="selected"<?php endif;?>>,</option>
                        <option value=";" <?php if (isset($importSettings['csv_separator']) && $importSettings['csv_separator'] == ';') : ?>selected="selected"<?php endif;?>>;</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="skipFirstRow" <?php if (isset($importSettings['skip_first_row']) && $importSettings['skip_first_row'] == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Skip first row')?></label>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
                    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                        'input_name'     => 'DefaultGroup[]',
                        'selected_id'    => isset($importSettings['user_groups_id']) ? $importSettings['user_groups_id'] : array(),
                        'multiple' 		 => true,
                        'css_class'       => 'form-control',
                        'list_function'  => 'erLhcoreClassModelGroup::getList',
                        'list_function_params'  => array()
                    )); ?>
                </div>
            </div>

            <div class="col-xs-3">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User department')?></label>
                    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                        'input_name'     => 'DepartmentGroup[]',
                        'selected_id'    => isset($importSettings['dep_id']) ? $importSettings['dep_id'] : array(),
                        'multiple' 		 => true,
                        'css_class'       => 'form-control',
                        'list_function'  => 'erLhcoreClassModelDepartament::getList',
                        'list_function_params'  => array()
                    )); ?>
                </div>
            </div>

            <div class="col-xs-3">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','File')?></label>
                    <input type="file" name="file" value="">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="oneRecordImport" <?php if (isset($importSettings['import_one']) && $importSettings['import_one'] == true) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Import only one record to test')?></label>
                </div>
            </div>
        </div>

        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Enter column number starting from 1')?></h4>

        <?php $attr = array(
                'username' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Username')),
                'password' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Password')),
                'email' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','E-mail')),
                'name' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','First name')),
                'surname' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Last name')),
                'chat_nickname' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Nickname')),
                'disabled' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Disabled')),
                'hide_online' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Hide Online')),
                'all_departments' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Can access all departments')),
                'skype' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Skype')),
                'job_title' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Job title')),
                'time_zone' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Time Zone')),
                'invisible_mode' => array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Invisible'))
            );
        ?>
        <div class="row">
            <?php foreach ($attr as $key => $item) : ?>
            <div class="col-xs-4">
                <div class="form-group">
                    <label><?php echo htmlspecialchars($item['name'])?></label>
                    <input type="number" class="form-control" name="field[<?php echo $key?>]" value="<?php (isset($importSettings['field'][$key])) ? print htmlspecialchars($importSettings['field'][$key]) : null ?>" />
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <input type="submit" class="btn btn-default" name="ImportAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Import');?>"/>
    </form>
</div>
