<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New brand');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<script>
    (function(){
        window.replaceConditions = <?php echo json_encode($item->conditions_array)?>;
        window.replaceDepartmentsRoles =  <?php echo json_encode($item->conditions_array_roles,JSON_FORCE_OBJECT)?>;
        window.replaceDepartments = <?php $items = []; foreach (erLhcoreClassModelDepartament::getList(['limit' => false]) as $itemDepartment) { $items[$itemDepartment->id] = $itemDepartment->name; }; echo json_encode($items) ?>;
    })();
</script>

<form ng-controller="BrandCtrl as crc" ng-init='crc.setConditions()' action="<?php echo erLhcoreClassDesign::baseurl('department/newbrand')?>" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhdepartment/formbrand.tpl.php'));?>

    <div class="btn-group btn-group-sm" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-secondary" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
