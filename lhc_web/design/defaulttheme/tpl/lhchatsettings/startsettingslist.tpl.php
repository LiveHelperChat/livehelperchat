<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','List of start chat settings')?></h1>

<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Department');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo htmlspecialchars($item->name)?></td>
        <td><?php echo htmlspecialchars($item->department)?></td>
        <td nowrap><a class="btn btn-secondary btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/copyfrom')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Clone');?></a></td>
        <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/editstartsettings')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit');?></a></td>
        <td><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/deletestartsettings')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','create')) : ?>
<div class="btn-group" role="group" aria-label="...">
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/newstartsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','New');?></a>
    <a class="btn btn-sm btn-outline-secondary csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/copyfrom/default')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Create a copy from default settings');?></a>
</div>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>
