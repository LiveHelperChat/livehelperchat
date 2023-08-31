<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhdepartment/parts/search_panel.tpl.php')); ?>

<table class="table table-sm table-hover" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','E-mail');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Hidden');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Disabled');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Visible only if online');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Overloaded');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Archived');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td nowrap="">
            <a href="<?php echo erLhcoreClassDesign::baseurl('department/edit')?>/<?php echo $departament->id?>"><?php echo $departament->id?></a>
            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','statisticdep')) : ?><a href="#" ng-click="lhc.openModal('statistic/departmentstats/<?php echo $departament->id?>')"><?php endif; ?>
            <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Load statistic');?>" class="material-icons text-info">donut_large</i>
            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','statisticdep')) : ?></a><?php endif; ?>

        </td>
        <td ng-non-bindable title="<?php echo $departament->sort_priority?>"><a class="d-block" href="<?php echo erLhcoreClassDesign::baseurl('department/edit')?>/<?php echo $departament->id?>"><?php echo htmlspecialchars($departament->name)?><span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Department alias');?>" class="ps-1 small text-muted"><?php echo htmlspecialchars($departament->alias);?></span></a></td>
        <td ng-non-bindable><?php echo htmlspecialchars($departament->email)?></td>
        <td ng-non-bindable><?php if ($departament->hidden == 1) : ?><span class="material-icons">visibility_off</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td ng-non-bindable><?php if ($departament->disabled == 1) : ?><span class="material-icons">block</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td ng-non-bindable><?php if ($departament->visible_if_online == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td ng-non-bindable><?php if ($departament->is_overloaded == true) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td ng-non-bindable><?php if ($departament->archive == 1) : ?><span class="material-icons">archive</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td nowrap ng-non-bindable>
            <a class="btn btn-secondary btn-xs action-image text-white" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'department/edit/<?php echo htmlspecialchars($departament->id)?>/(action)/operators'})" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Assigned operators');?></a>
        </td>
        <td nowrap ng-non-bindable><a class="btn btn-secondary csfr-required btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('department/clone')?>/<?php echo $departament->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Clone');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','create')) : ?>
<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('department/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?></a>
<?php endif;?>