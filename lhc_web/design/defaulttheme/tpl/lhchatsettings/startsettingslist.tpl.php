<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','List of start chat settings')?></h1>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Department');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo htmlspecialchars($item->name)?></td>
        <td><?php echo htmlspecialchars($item->department)?></td>
        <td nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/editstartsettings')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit');?></a></td>
        <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/deletestartsettings')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','create')) : ?>
<a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/newstartsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','New');?></a>
<?php endif;?>