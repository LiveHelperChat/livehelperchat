<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Groups');?></legend>

<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th>ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Title');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
<?php foreach (erLhcoreClassGroup::getGroupList() as $user) : ?>
    <tr>
        <td width="1%"><?php echo $user['id']?></td>
        <td><?php echo $user['name']?></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('user/editgroup/'.$user['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Edit group');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Edit group');?>" /></a></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('user/deletegroup/'.$user['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Delete group');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Delete group');?>" /></a></td>
    </tr>
<?php endforeach; ?>
</table>
<br />

<div>
<a href="<?php echo erLhcoreClassDesign::baseurl('user/newgroup/')?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','New group');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','New group');?>" /></a>
</div>
</fieldset>