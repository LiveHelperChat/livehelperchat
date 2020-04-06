<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Languages')?></h1>

<table class="table" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Language');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Siteaccess');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><?php echo $item->id?></td>
            <td><?php echo htmlspecialchars($item->name)?></td>
            <td><?php echo htmlspecialchars($item->siteaccess)?></td>
            <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('speech/editlanguage')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('speech/newlanguage')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>