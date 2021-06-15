
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Rest API Calls')?></h1>

<?php if (isset($imported_rest)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Rest API imported'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($items)) : ?>

    <table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Name');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Download')?>" href="?id=<?php echo $item->id?>"><i class="material-icons">cloud_download</i></a>
                    <a title="<?php echo $item->id?>" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editrestapi')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editrestapi')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/deleterestapi')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
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
        <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/newrestapi')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <div class="form-group">
        <input title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','File')?> (json)" accept=".json" type="file" class="form-control-file" name="restfile" value="" />
    </div>
    <input type="submit" name="ImportRestAPI" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import')?>" />
</form>


