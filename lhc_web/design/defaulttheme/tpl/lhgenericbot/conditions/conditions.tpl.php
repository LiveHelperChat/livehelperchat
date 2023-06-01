<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Conditions')?></h1>

<?php if (isset($items)) : ?>

    <table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Identifier');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editcondition')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td>
                    <?php echo htmlspecialchars($item->identifier)?>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/editcondition')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/deletecondition')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/newcondition')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>
