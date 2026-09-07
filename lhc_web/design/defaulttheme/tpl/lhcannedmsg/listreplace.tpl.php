<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Replaceable variables');?></h1>

<br/>
<table class="table table-sm" cellpadding="0" cellspacing="0" ng-non-bindable>
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Identifier');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php
        $hasSensitiveAccess = erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use_replace_sensitive');
    ?>
    <?php foreach ($items as $item) :
        $itemConfiguration = $item->configuration_array;
        $itemRestricted = !empty($itemConfiguration['restricted_access']);
        $itemHidden = !empty($itemConfiguration['hide_in_list']);
        $itemNotManaged = $itemRestricted && !$hasSensitiveAccess;
        $itemValueHidden = $itemHidden || $itemNotManaged;
    ?>
        <tr>
            <td>
                <?php echo htmlspecialchars($item->identifier)?>
                <?php if ($itemRestricted) : ?><span class="badge bg-danger ms-1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Only operators with manage sensitive replaceable variables permission can manage this variable');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Restricted');?></span><?php endif; ?>
            </td>
            <td>
                <?php if ($itemValueHidden) : ?>
                    <span class="text-muted material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Hidden');?>">visibility_off</span>
                <?php else : ?>
                    <?php echo htmlspecialchars(mb_substr($item->default,0,100))?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item->is_active == 1) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active');?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','In-Active');?>
                <?php endif; ?>
                <?php if ($item->time_zone != '') : ?>
                <span class="badge bg-info">
                    <?php
                        echo (new DateTime('now',($item->time_zone != '' ? new DateTimeZone($item->time_zone) : null)))->format('Y-m-d H:i:s'),', ',$item->time_zone;
                    ?>
                </span>
                <?php endif; ?>
            </td>
            <td nowrap ng-non-bindable>
                <?php if (!$itemNotManaged) : ?><a class="btn btn-secondary csfr-required btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/clonereplace')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cannedmsg/deletereplace','Clone');?></a><?php endif; ?>
            </td>
            <td nowrap>
                <?php if (!$itemNotManaged) : ?><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/editreplace')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit');?></a><?php endif; ?>
            </td>
            <td nowrap>
                <?php if (!$itemNotManaged) : ?><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/deletereplace')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete');?></a><?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a class="btn btn-sm btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('cannedmsg/newreplace')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New');?></a>