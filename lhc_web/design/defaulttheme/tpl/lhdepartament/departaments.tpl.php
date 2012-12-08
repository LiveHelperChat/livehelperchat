<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments');?></legend>

<table class="lentele" cellpadding="0" cellspacing="0">
<tr>
    <th>ID</th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Name');?></th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
<? foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <tr>
        <td><?=$departament['id']?></td>
        <td><?=$departament['name']?></td>
        <td><a href="<?=erLhcoreClassDesign::baseurl('departament/edit/'.$departament['id'])?>"><img src="<?=erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit department');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit department');?>" /></a></td>
        <td><a href="<?=erLhcoreClassDesign::baseurl('departament/delete/'.$departament['id'])?>"><img src="<?=erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Delete department');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Delete department');?>" /></a></td>
    </tr>
<? endforeach; ?>
</table>
<br />

<div>
<a href="<?=erLhcoreClassDesign::baseurl('departament/new/')?>"><img src="<?=erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New department');?>" /></a>
</div>
</fieldset>