<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Translations items')?></h1>

<?php if (isset($items)) : ?>
    <table class="table table-sm" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Identifier');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <a title="<?php echo $item->id?>" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edittritem')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->identifier)?></a>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edittritem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/deletetritem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/newtritem')?><?php if (isset($input->group_id) && $input->group_id > 0) : ?>/(group_id)/<?php echo (int)$input->group_id?><?php endif; ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>
