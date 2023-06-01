<h1>Manage departments and their groups</h1>

<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('department/departments')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','managegroups')) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('department/group')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments groups');?></a></li>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('department/limitgroup')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments limit groups');?></a></li>
    <?php endif; ?>

    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','managebrands')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('department/brands')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Brands');?></a></li>
    <?php endif; ?>
</ul>