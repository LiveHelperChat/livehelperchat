<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Brands');?></h1>

<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Brand');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><a href="<?php echo erLhcoreClassDesign::baseurl('department/editbrand')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a></td>
            <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('department/editbrand')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit');?></a></td>
            <td nowrap><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('department/deletebrand')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','managebrands')) : ?>
    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('department/newbrand')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
<?php endif;?>