<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Documents list');?></h1>

<?php if ($pages->items_total > 0) : ?>
	<table class="twelve" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Name');?></th>
	    <th class="one"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Active');?></th>
	    <th nowrap width="1%" ></th>
	    <th nowrap width="1%" ></th>
	</tr>
	</thead>
	<?php foreach ($items as $item) : ?>
	    <tr>
	        <td><?php echo $item->id; ?></td>
	        <td><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/edit')?>/<?php echo $item->id; ?>"><?php echo htmlspecialchars($item->name)?></a></td>	 	       
	        <td><?php if ($item->active == 1) : ?><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Y');?></b><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','N');?><?php endif;?></td>
	        <td nowrap><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('docshare/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Edit');?></a></td>
	        <td nowrap class="right"><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="small alert button round csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('docshare/delete')?>/<?php echo $item->id; ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','Delete this document');?></a></td>
	    </tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a href="<?php echo erLhcoreClassDesign::baseurl('docshare/new')?>" class="button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/list','New document');?></a>




