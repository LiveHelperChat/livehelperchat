<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users');?></legend>

<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <th>ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','E-mail');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Surname');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
<?php foreach (erLhcoreClassUser::getUserList() as $user) : ?>
    <tr>
        <td><?php echo $user['id']?></td>
        <td><?php echo $user['username']?></td>
        <td><?php echo $user['email']?></td>
        <td><?php echo $user['name']?></td>
        <td><?php echo $user['surname']?></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('user/edit/'.$user['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/page_edit.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit user');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit user');?>" /></a></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('user/delete/'.$user['id'])?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete user');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete user');?>" /></a></td>
    </tr>
<?php endforeach; ?>
</table>
<br />

<div>
<a href="<?php echo erLhcoreClassDesign::baseurl('user/new/')?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','New user');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','New user');?>" /></a>
</div>
</fieldset>