<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bot individualization')?></h1>

<?php if (isset($items)) : ?>
    <table class="table table-sm" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
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
                    <a title="<?php echo $item->id?>" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/listtranslationsitems')?>/(group_id)/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a>
                    <?php if ($item->use_translation_service == 1) : ?>
                        <span class="badge bg-info" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Automatic translation enabled')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Auto translate')?></span>
                    <?php endif; ?>
                    <?php if (!empty($item->configuration_array['detect_chat_language'])) : ?>
                        <span class="badge bg-warning" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Chat language detection enabled')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Detect language')?></span>
                    <?php endif; ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhaudit/modifyinfo.tpl.php')); ?>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/edittrgroup')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/deletetrgroup')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<a class="btn btn-sm btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('genericbot/newtrgroup')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','New')?></a>
