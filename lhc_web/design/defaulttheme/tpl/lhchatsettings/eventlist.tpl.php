<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','List of events tracking settings by department')?></h1>

<?php $gaOptions = erLhcoreClassModelChatConfig::fetch('ga_options')->data_value;
if (!isset($gaOptions['ga_enabled']) || $gaOptions['ga_enabled'] == false) : ?>
    <?php $errors = [erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Please enable events tracking first!')]; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php else : ?>
    <table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
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
                <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/editeventsettings')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/deleteevent')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','create')) : ?>
        <a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('chatsettings/neweventsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','New');?></a>
    <?php endif;?>
<?php endif; ?>

