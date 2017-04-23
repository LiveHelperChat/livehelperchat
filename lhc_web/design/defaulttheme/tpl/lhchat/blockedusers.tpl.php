<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/blockedusers.tpl.php'));?>

<?php if (isset($block_saved) && $block_saved == true) : ?>
		<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Updated'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"  method="post">
<div class="row">
	<div class="col-xs-4">
		<input type="text" class="form-control" name="IPToBlock" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP');?>" />
	</div>	
	<div class="col-xs-8">
		<input type="submit" class="btn btn-default" name="AddBlock" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>" />
	</div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
</form>

<?php if (!empty($items)) : ?>
<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Date');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Users who are blocked');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td><?php echo $departament->id?></td>
        <td><?php echo htmlspecialchars($departament->ip)?></td>
        <td><?php echo htmlspecialchars($departament->datets_front)?></td>
        <td><?php echo htmlspecialchars($departament->user)?></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>/(remove_block)/<?php echo $departament->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Remove block');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Empty...');?></p>
<?php endif; ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>