<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhdepartment/parts/search_panel.tpl.php')); ?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','E-mail');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Hidden');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Disabled');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Visible only if online');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Overloaded');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td><?php echo $departament->id?></td>
        <td><?php echo htmlspecialchars($departament->name)?></td>
        <td><?php echo htmlspecialchars($departament->email)?></td>
        <td><?php if ($departament->hidden == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td><?php if ($departament->disabled == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td><?php if ($departament->visible_if_online == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td><?php if ($departament->is_overloaded == true) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','No');?><?php endif;?></td>
        <td nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('department/edit')?>/<?php echo $departament->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit department');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','create')) : ?>
<a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('department/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?></a>
<?php endif;?>