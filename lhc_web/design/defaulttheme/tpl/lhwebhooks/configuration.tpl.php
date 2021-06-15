<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Webhooks list')?></h1>

<?php
$cfg = erConfigClassLhConfig::getInstance();
$webhooksEnabled = $cfg->getSetting( 'webhooks', 'enabled', false );
?>

<?php if ($webhooksEnabled === false) : $errors = array('Webhooks calls are not enabled!')?>
     <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($items)) : ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Event');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Bot');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Trigger');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Enabled');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td nowrap="nowrap">
                    <?php if ($item->type == 0) : ?>
                        <?php echo htmlspecialchars($item->event)?>
                    <?php elseif ($item->type == 1) : ?>
                        <b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Continuous event');?></b>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($item->bot)?></td>
                <td><?php echo htmlspecialchars($item->trigger)?></td>
                <td nowrap="nowrap">
                    <?php echo htmlspecialchars($item->disabled == 1 ? 'N' : 'Y')?>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/delete')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/new')?>" class="btn btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>

