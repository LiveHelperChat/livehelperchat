<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Archives list');?></h1>

<table class="twelve" cellpadding="0" cellspacing="0">
<thead>
	<tr>
	    <th width="1%">ID</th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','From date');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Till date');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Chats in archive');?></th>
	    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Messages in archive');?></th>
	    <th width="2%">&nbsp;</th>
	    <th width="2%">&nbsp;</th>
	</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><?php echo htmlspecialchars($item->range_from_front)?></td>
        <td><?php echo htmlspecialchars($item->range_to_front)?></td>
        <td><?php echo htmlspecialchars($item->chats_in_archive)?></td>
        <td><?php echo htmlspecialchars($item->messages_in_archive)?></td>
        <td nowrap="nowrap"><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/process')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Process again');?></a></td>
        <td nowrap="nowrap"><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('departament/edit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Edit');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>


<a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('departament/new')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','New archive');?></a>
