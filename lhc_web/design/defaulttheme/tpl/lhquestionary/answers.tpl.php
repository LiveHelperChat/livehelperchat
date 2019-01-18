<?php if ($pages->items_total > 0) : ?>
	<table class="table" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','Answer');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','Date');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','IP');?></th>
	    <th width="1%">&nbsp;</th>
	    <th width="1%">&nbsp;</th>
	</tr>
	</thead>
	<?php foreach ($items as $item) : ?>
	    <tr>
	        <td><?php echo $item->id?></td>
	        <td><?php echo htmlspecialchars(erLhcoreClassDesign::shrt($item->answer))?></td>
	        <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$item->ctime)?></td>
	        <td><?php echo htmlspecialchars($item->ip_front)?></td>
	        <td nowrap><a class="btn btn-default btn-xs" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('questionary/previewanswer')?>/<?php echo $item->id?>','height':'500','iframe':true})" href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','View');?></a></td>
	        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('questionary/deleteanswer')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','Delete the answer');?></a></td>
	    </tr>
	<?php endforeach; ?>
	</table>

	<?php if (isset($pages)) : ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
	<?php endif;?>

<?php endif; ?>

<?php if ($question->is_voting == 1) : ?>
<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th class="large-5"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','Option name');?></th>
    <th class="large-5"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','Progress');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/answers','Votes');?></th>
</tr>
</thead>
<?php foreach ($optionsItems as $optionsItem) : ?>
    <tr>
        <td><?php echo $optionsItem->id?></td>
        <td><?php echo htmlspecialchars($optionsItem->option_name)?></td>
        <td><div class="progress ten"><span style="width: <?php echo ($optionsItem->votes/$question->total_votes_for_percentange)*100 ?>%" class="meter"></span></div></td>
        <td>(<?php echo $optionsItem->votes?>)</td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif;?>