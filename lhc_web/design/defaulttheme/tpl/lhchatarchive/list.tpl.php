<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list');?></h1>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','From date');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Till date');?></th>
	    <th width="2%">&nbsp;</th>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchatarchive','configuration')) : ?>
	    <th width="2%">&nbsp;</th>
	    <th width="2%">&nbsp;</th>
        <?php endif; ?>
	</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><?php echo htmlspecialchars($item->range_from_front)?> <?php if ($item->first_id > 0) : ?>[<?php echo $item->first_id?>]<?php endif;?></td>
        <td><?php echo htmlspecialchars($item->range_to_front)?> <?php if ($item->last_id > 0) : ?>[<?php echo $item->last_id?>]<?php endif;?></td>
        <td nowrap="nowrap"><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/listarchivechats')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','List chats');?></a></td>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchatarchive','configuration')) : ?>
        <td nowrap="nowrap"><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/process')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Process again');?></a></td>
        <td nowrap="nowrap"><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Edit');?></a></td>
        <?php endif; ?>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchatarchive','configuration')) : ?>
<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/newarchive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','New archive');?></a>
<?php endif; ?>
