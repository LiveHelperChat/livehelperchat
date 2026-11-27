<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list');?></h1>

<table ng-non-bindable class="table" cellpadding="0" cellspacing="0">
<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Name');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','From date');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Till date');?></th>
	    <th width="2%">&nbsp;</th>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailarchive','configuration')) : ?>
	    <th width="2%">&nbsp;</th>
	    <th width="2%">&nbsp;</th>
        <?php endif; ?>
	</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td <?php if ($item->type != \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>colspan="3" <?php endif;?> >
            <?php if ($item->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>
                <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Backup');?>">archive</span>
            <?php else : ?>
                <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archive');?>">backup</span>
            <?php endif; ?>
            <?php echo htmlspecialchars($item->name)?>
        </td>
        <?php if ($item->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>
        <td><?php echo htmlspecialchars($item->range_from_front)?> <?php if ($item->first_id > 0) : ?>[<?php echo $item->first_id?>]<?php endif;?></td>
        <td><?php echo htmlspecialchars($item->range_to_front)?> <?php if ($item->last_id > 0) : ?>[<?php echo $item->last_id?>]<?php endif;?></td>
        <?php endif; ?>

        <td nowrap="nowrap"><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/listarchivemails')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','List mails');?></a></td>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailarchive','configuration')) : ?>
        <td nowrap="nowrap">
            <?php if ($item->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) : ?>
            <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/process')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Process again');?></a>
            <?php endif; ?>
        </td>
        <td nowrap="nowrap"><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Edit');?></a></td>
        <?php endif; ?>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailarchive','configuration')) : ?>
<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/newarchive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','New archive');?></a>
<?php endif; ?>
