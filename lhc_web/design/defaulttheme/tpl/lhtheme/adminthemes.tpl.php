<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Admin themes');?></h1>

<?php if ($pages->items_total > 0) : ?>
<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Operator');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('theme/adminthemeedit')?>/<?php echo $item->id?>">
                <?php if ($item->name == '') : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','My theme'); ?>
                <?php else : ?>
                    <?php echo htmlspecialchars($item->name)?>
                <?php endif; ?>
            </a></td>
        <td><?php echo htmlspecialchars($item->user)?></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('theme/adminthemedelete')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Empty...');?></p>
<?php endif;?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('theme/adminnewtheme')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
