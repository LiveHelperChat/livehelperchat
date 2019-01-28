<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bot exceptions groups')?></h1>

<?php if (isset($items)) : ?>

    <table class="table" cellpadding="0" cellspacing="0" width="100%">
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
                    <a title="<?php echo $item->id?>" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editexception')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editexception')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/deleteexception')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/newexception')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>
