<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Questions');?></h1>

<?php if ($pages->items_total > 0) : ?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Question');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Location');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Priority');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Active');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Revote');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/edit')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->question)?></a></td>
        <td><?php echo htmlspecialchars($item->location)?></td>
        <td><?php echo $item->priority?></td>
        <td><?php if ($item->active == 1) : ?><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Y');?></b><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','N');?><?php endif;?></td>
        <td><?php echo $item->revote > 0 ? $item->revote : erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Off') ; ?></td>
        <td nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('questionary/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Edit the question');?></a></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('questionary/delete')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Delete the question');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Empty...');?></p>
<?php endif;?>

<a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('questionary/newquestion')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','New question');?></a>
