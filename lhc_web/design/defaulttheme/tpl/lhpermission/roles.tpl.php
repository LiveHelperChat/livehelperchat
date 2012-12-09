<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','List of roles');?></h1>

<table class="lentele" cellpadding="0" cellspacing="0">
<tr>
    <th>ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Title');?></th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
<?php foreach (erLhcoreClassRole::getRoleList() as $departament) : ?>
    <tr>
        <td><?php echo $departament['id']?></td>
        <td><?php echo $departament['name']?></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('permission/editrole/'.$departament['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Edit role');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Edit role');?>" /></a></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('permission/deleterole/'.$departament['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Delete role');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Delete role');?>" /></a></td>
    </tr>
<?php endforeach; ?>
</table>
<br />

<div>
<a href="<?php echo erLhcoreClassDesign::baseurl('permission/newrole/')?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','New role');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','New role');?>" /></a>
</div>