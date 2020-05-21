<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Group chat');?></h1>

<?php if (isset($items)) : ?>
<table class="table" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Operator');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Type');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Creation time');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><?php echo htmlspecialchars($item->name)?></td>
            <td><?php echo htmlspecialchars($item->user)?></td>
            <td><?php if ($item->type == erLhcoreClassModelGroupChat::PUBLIC_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Public');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Private');?><?php endif;?></td>
            <td><?php echo htmlspecialchars($item->time_front)?></td>
            <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('groupchat/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttoms','Edit');?></a></td>
            <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('groupchat/delete')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('groupchat/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
