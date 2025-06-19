<?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/search_panel_mailinglist.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Members');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','User');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/editmailinglist')?>/<?php echo $item->id?>" ><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/mailingrecipient')?>/(ml)/<?php echo $item->id?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','List of members');?></a>
                </td>
                <td>
                    <?php echo htmlspecialchars($item->user instanceof erLhcoreClassModelUser ? $item->user : ''); ?>
                </td>
                <td>
                    <a class="text-danger csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/deletemailinglist')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/newmailinglist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>