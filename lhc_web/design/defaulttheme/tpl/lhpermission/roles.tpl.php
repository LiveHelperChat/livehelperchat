<div class="header-list"><h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','List of roles');?></h1></div>

<table class="lentele" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Title');?></th>
    <th width="5%">&nbsp;</th>
    <th width="5%">&nbsp;</th>
</tr>
</thead>
<?php foreach (erLhcoreClassRole::getRoleList() as $departament) : ?>
    <tr>
        <td><?php echo $departament['id']?></td>
        <td><?php echo $departament['name']?></td>
        <td nowrap><a class="tiny button round" href="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $departament['id']?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Edit role');?></a></td>
        <td nowrap><a class="tiny alert button round" onclick="return confirm('Are you sure?')" href="<?php echo erLhcoreClassDesign::baseurl('permission/deleterole')?>/<?php echo $departament['id']?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Delete role');?></a></td>
    </tr>
<?php endforeach; ?>
</table>
<br />

<a class="button small" href="<?php echo erLhcoreClassDesign::baseurl('permission/newrole')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','New role');?></a>
