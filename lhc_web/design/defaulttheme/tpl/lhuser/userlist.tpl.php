<h1><?php include(erLhcoreClassDesign::designtpl('lhuser/titles/userlist.tpl.php')); ?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/search_panel.tpl.php')); ?>

<?php
	$canEdit = $currentUser->hasAccessTo('lhuser','edituser');
	$canDelete = $currentUser->hasAccessTo('lhuser','deleteuser');
	$canLoginAs = $currentUser->hasAccessTo('lhuser','loginas');
?>
<table class="table table-sm list-links" cellpadding="0" ng-non-bindable cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username (Nickname)');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','E-mail');?></th>
    <th title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Maximum number of chats operator can have.');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Number of chats');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last login');?></th>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/column_multiinclude.tpl.php')); ?>
    <?php if ($canLoginAs) : ?><th width="1%">&nbsp;</th><?php endif;?>
    <?php if ($canDelete) : ?><th width="1%">&nbsp;</th><?php endif;?>
</tr>
</thead>
<?php foreach ($userlist as $user) : ?>
    <tr class="<?php if ($user->disabled == 1) : ?>text-muted<?php endif;?>">
        <td><?php echo $user->id?></td>
        <td>
            <?php if ($user->disabled == 1) : ?>
                <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','This user is disabled');?>">person_off</span>
            <?php endif; ?>

            <?php if ($user->force_logout == 1) : ?>
                <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','This user is forced to logout');?>">logout</span>
            <?php endif; ?>

            <?php if ($currentUser->hasAccessTo('lhstatistic','userstats')) : ?>
                <a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','See operator statistic')?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'statistic/userstats/<?php echo htmlspecialchars($user->id)?>'})">
                    <span class="material-icons">bar_chart</span>
                </a>
            <?php endif; ?>

            <?php if ($canEdit) : ?><a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>"><?php endif; ?><?php echo htmlspecialchars($user->username)?><?php echo htmlspecialchars($user->chat_nickname !== '' ? ' ('. $user->chat_nickname .')' : '')?><?php if ($canEdit) : ?></a><?php endif; ?>
        </td>
        <td><?php echo htmlspecialchars($user->email)?></td>
        <td>
            <?php if ($user->exclude_autoasign == 1) : ?>
                <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Excluded from auto assign workflow');?>" class="material-icons chat-closed me-1">block</span>
            <?php endif; ?>
            <?php if ($user->max_active_chats == 0) : ?>
                &#8734;
            <?php else : ?>
                <?php echo htmlspecialchars($user->max_active_chats)?>
            <?php endif; ?>
        </td>
        <td>
            <?php if ($user->lastactivity > 0) : ?>
            <?php echo $user->lastactivity_ago?>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','ago');?>
            <?php else : ?>
            -
            <?php endif; ?>
        </td>
        <td>
            <?php if ($user->llogin > 0) : ?>
                <?php echo $user->llogin_ago?>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','ago');?>
            <?php else : ?>
                -
            <?php endif; ?>
        </td>
        <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/column_data_multiinclude.tpl.php')); ?>
        <?php if ($canLoginAs) : ?>
            <td nowrap=""><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/loginas')?>/<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Login As');?></a></td>
        <?php endif;?>
        <?php if ($canDelete) : ?><td><?php if ($user->id != 1) : ?><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('user/delete')?>/<?php echo $user->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a><?php endif;?></td><?php endif;?>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<br />

<?php if ($currentUser->hasAccessTo('lhuser','createuser')) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/userlist/new.tpl.php')); ?>
<?php endif; ?>