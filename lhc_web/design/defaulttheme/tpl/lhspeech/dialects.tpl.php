<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Dialects');?></h1>

<table class="table" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Language');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Dialect');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Language Code');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Short Code');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><?php echo $item->id?></td>
            <td><?php echo htmlspecialchars($item->language)?></td>
            <td><?php echo htmlspecialchars($item->lang_name)?></td>
            <td><?php echo htmlspecialchars($item->lang_code)?></td>
            <td><?php echo htmlspecialchars($item->short_code)?></td>
            <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('speech/editdialect')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit');?></a></td>
            <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('speech/deletedialect')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('speech/newdialect')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','New dialect');?></a>