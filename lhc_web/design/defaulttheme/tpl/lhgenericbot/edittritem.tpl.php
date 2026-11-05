<h1 ng-non-bindable><?php echo htmlspecialchars($item->identifier)?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/form_translation_item.tpl.php'));?>

    <div class="card mb-2">
        <div class="card-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Copy to other individualization groups');?></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Copy mode');?></label>
                        <select name="copy_mode" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Do not copy');?></option>
                            <option value="create" <?php if (isset($_POST['copy_mode']) && $_POST['copy_mode'] == 'create') : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Create if not exists');?></option>
                            <option value="update" <?php if (isset($_POST['copy_mode']) && $_POST['copy_mode'] == 'update') : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Create/Update');?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Individualization groups');?></label>
                        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                            'input_name'     => 'copy_group_ids[]',
                            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Choose individualization groups'),
                            'selected_id'    => isset($_POST['copy_group_ids']) && is_array($_POST['copy_group_ids']) ? array_map('intval', $_POST['copy_group_ids']) : array(),
                            'css_class'      => 'form-control',
                            'display_name'   => 'name',
                            'list_function_params' => array('sort' => '`name` ASC', 'limit' => false),
                            'list_function'  => 'erLhcoreClassModelGenericBotTrGroup::getList'
                        )); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Update_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Save_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save & Exit');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_bot" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>

<script>
    $(function() {
        $('form').find('.btn-block-department').makeDropdown();
    });
</script>
