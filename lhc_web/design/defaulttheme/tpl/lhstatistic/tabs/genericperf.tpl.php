<?php if (isset($genericperfStatistic) && !empty($genericperfStatistic)) : ?>

<table class="table table-sm" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
    <thead>
    <tr>
        <th width="5%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','ID');?></th>
        <th width="70%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Type');?></th>
        <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Created at');?></th>
        <th width="5%"></th>
    </tr>
    </thead>
    <?php foreach ($genericperfStatistic as $item) : ?>
        <tr>
            <td><?php echo (int)$item->id; ?>&nbsp;<a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'statistic/statistic/(tab)/genericperf?previewItem=<?php echo (int)$item->id; ?>'}); return false;" class="btn btn-xs btn-outline-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Preview');?></a></td>
            <td><?php echo $item->type == \LiveHelperChat\Models\Statistic\Performance::DEPARTMENT ? erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Department') : erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Operator'); ?></td>
            <td><?php echo $item->created_at > 0 ? date('Y-m-d H:i:s', $item->created_at) : '-'; ?></td>
            <td nowrap><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/genericperf/(delete_item)/<?php echo (int)$item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif; ?>

<?php else : ?>
<p class="alert alert-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','No data found');?></p>
<?php endif; ?>