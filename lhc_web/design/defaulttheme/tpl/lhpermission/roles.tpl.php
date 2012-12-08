<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','List of roles');?></legend>

<table class="lentele" cellpadding="0" cellspacing="0">
<tr>
    <th>ID</th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Title');?></th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
<? foreach (erLhcoreClassRole::getRoleList() as $departament) : ?>
    <tr>
        <td><?=$departament['id']?></td>
        <td><?=$departament['name']?></td>
        <td><a href="<?=erLhcoreClassDesign::baseurl('permission/editrole/'.$departament['id'])?>"><img src="<?=erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Edit role');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Edit role');?>" /></a></td>
        <td><a href="<?=erLhcoreClassDesign::baseurl('permission/deleterole/'.$departament['id'])?>"><img src="<?=erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Delete role');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Delete role');?>" /></a></td>
    </tr>
<? endforeach; ?>
</table>
<br />

<div>
<a href="<?=erLhcoreClassDesign::baseurl('permission/newrole/')?>"><img src="<?=erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','New role');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','New role');?>" /></a>
</div>
</fieldset>