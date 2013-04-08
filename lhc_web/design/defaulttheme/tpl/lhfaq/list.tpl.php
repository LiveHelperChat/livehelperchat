<?php if ($pages->items_total > 0) : ?>
	<table class="twelve" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Name');?></th>
	    <th nowrap width="1%" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Delete question');?></th>
	</tr>
	</thead>
	<?php foreach ($items as $item) : ?>
	    <tr>
	        <td><?php echo $item->id; ?></td>
	        <td><a href="<?php echo erLhcoreClassDesign::baseurl('faq/view')?>/<?php echo $item->id; ?>"><?php echo htmlspecialchars($item->question)?></a></td>
	        <td nowrap class="right"><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="tiny alert button round" href="<?php echo erLhcoreClassDesign::baseurl('faq/delete')?>/<?php echo $item->id; ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','Delete this question');?></a></td>
	    </tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>

<a href="<?php echo erLhcoreClassDesign::baseurl('faq/new')?>" class="button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','New question');?></a>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>


