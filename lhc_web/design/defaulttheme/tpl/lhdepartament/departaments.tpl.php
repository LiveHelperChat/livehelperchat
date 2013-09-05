<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments');?></h1>

<table class="twelve" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','E-mail');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td><?php echo $departament->id?></td>
        <td><?php echo htmlspecialchars($departament->name)?></td>
        <td><?php echo htmlspecialchars($departament->email)?></td>
        <td nowrap><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('departament/edit')?>/<?php echo $departament->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit department');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>


<a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('departament/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?></a>
