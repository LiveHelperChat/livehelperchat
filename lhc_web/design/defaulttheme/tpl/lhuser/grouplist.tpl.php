<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Groups');?></legend>

<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th>ID</th>
    <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Title');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
<? foreach (erLhcoreClassGroup::getGroupList() as $user) : ?>
    <tr>
        <td width="1%"><?=$user['id']?></td>
        <td><?=$user['name']?></td>
        <td><a href="<?=erLhcoreClassDesign::baseurl('user/editgroup/'.$user['id'])?>"><img src="<?=erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Edit group');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Edit group');?>" /></a></td>
        <td><a href="<?=erLhcoreClassDesign::baseurl('user/deletegroup/'.$user['id'])?>"><img src="<?=erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Delete group');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Delete group');?>" /></a></td>
    </tr>
<? endforeach; ?>
</table>
<br />

<div>
<a href="<?=erLhcoreClassDesign::baseurl('user/newgroup/')?>"><img src="<?=erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','New group');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','New group');?>" /></a>
</div>
</fieldset>