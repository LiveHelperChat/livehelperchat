<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','FAQ');?></h1>

<?php if ($pages->items_total > 0) : ?>
	<table class="table" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Question');?></th>
	    <th class="one"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Identifier');?></th>
	    <th class="one"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Active');?></th>
	    <th nowrap width="1%" ></th>
	    <th nowrap width="1%" ></th>
	</tr>
	</thead>
	<?php foreach ($items as $item) : ?>
	    <tr>
	        <td><?php echo $item->id; ?></td>
	        <td><a href="<?php echo erLhcoreClassDesign::baseurl('faq/view')?>/<?php echo $item->id; ?>"><?php echo htmlspecialchars($item->question)?></a></td>
	        <td><?php echo htmlspecialchars($item->identifier)?></td>	       
	        <td><?php if ($item->active == 1) : ?><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Y');?></b><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','N');?><?php endif;?></td>
	        <td nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('faq/view')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Edit');?></a></td>
	        <td nowrap class="right"><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="btn btn-danger btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('faq/delete')?>/<?php echo $item->id; ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Delete this question');?></a></td>
	    </tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a href="<?php echo erLhcoreClassDesign::baseurl('faq/new')?>" class="btn btn-default"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','New question');?></a>




