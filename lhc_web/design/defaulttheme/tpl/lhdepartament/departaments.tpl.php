<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments');?></legend>

<table class="lentele" cellpadding="0" cellspacing="0">
<tr>
    <th>ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Name');?></th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <tr>
        <td><?php echo $departament['id']?></td>
        <td><?php echo $departament['name']?></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('departament/edit/'.$departament['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit department');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit department');?>" /></a></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('departament/delete/'.$departament['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Delete department');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Delete department');?>" /></a></td>
    </tr>
<?php endforeach; ?>
</table>
<br />

<div>
<a href="<?php echo erLhcoreClassDesign::baseurl('departament/new/')?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?>" /></a>
</div>
</fieldset>