<?php if ($userDep instanceof erLhcoreClassModelUserDep) : ?>
    <?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','Assign department'); ?>
<?php else : ?>
    <?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','Assign department group'); ?>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <script>$.get('<?php echo erLhcoreClassDesign::baseurl('user/userdepartments')?>/<?php echo $user->id?>',function(data){$('#departments').html(data);})</script>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>

<form enctype="multipart/form-data" action="<?php echo erLhcoreClassDesign::baseurl('user/newdepartment')?>/<?php echo $user->id?><?php if ($userDep instanceof erLhcoreClassModelDepartamentGroupUser) : ?>/(mode)/group<?php endif; ?><?php if (isset($editor) && $editor == 'self') : ?>/(editor)/self<?php endif; ?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
    <div class="form-group drop-down-modal" >
        <?php if ($userDep instanceof erLhcoreClassModelUserDep) : ?>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'dep_ids',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department you want to add'),
                'selected_id'    => [$userDep->dep_id],
                //'data_prop'      => 'data-type="radio" data-limit="1"',
                'ajax'           => 'deps',
                //'type'           => 'radio',
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params' => ['filternotin' => ['id' => $present_dep_ids], 'sort' => '`name` ASC', 'limit' => 50, 'filter' => ['archive' => 0], 'filterin' => ['id' => $dep_ids]],
                'list_function'  => 'erLhcoreClassModelDepartament::getList'
            )); ?>
        <?php else : ?>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'dep_ids',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group you want to add'),
                'selected_id'    => [$userDep->dep_group_id],
                //'data_prop'      => 'data-type="radio" data-limit="1"',
                //'type'           => 'radio',
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params' => ['filternotin' => ['id' => $present_dep_ids], 'sort' => '`name` ASC', 'limit' => false, 'filterin' => ['id' => $dep_group_ids]],
                'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
            )); ?>
        <?php endif; ?>
    </div>

    <hr>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/department/attributes.tpl.php'));?>

    <button type="submit" class="btn btn-sm btn-secondary" name="update"><span class="material-icons me-0">add</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/assigndepartment','Add')?></button>
    <script>
        $('.drop-down-modal').makeDropdown();
    </script>
</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>