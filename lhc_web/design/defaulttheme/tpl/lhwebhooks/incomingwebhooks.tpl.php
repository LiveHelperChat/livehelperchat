<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Incoming webhooks list')?></h1>

<?php if (isset($imported_rest)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Webhook imported'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($items)) : ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Name');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Identifier');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Disabled');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td nowrap="nowrap">
                    <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Download')?>" href="?id=<?php echo $item->id?>"><i class="material-icons">cloud_download</i></a>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/editincoming')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td><?php echo htmlspecialchars($item->identifier)?></td>
                <td nowrap="nowrap"><?php echo htmlspecialchars($item->disabled == 1 ? 'Y' : 'N')?></td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/editincoming')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/deleteincoming')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<form action="" method="post" class="form-inline" autocomplete="off" enctype="multipart/form-data">
    <div class="form-group mr-2">
        <a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/newincoming')?>" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <div class="form-group">
        <input title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','File')?> (json)" accept=".json" type="file" class="form-control-file" name="restfile" value="" />
    </div>
    <input type="submit" name="ImportRestAPI" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import')?>" />
</form>