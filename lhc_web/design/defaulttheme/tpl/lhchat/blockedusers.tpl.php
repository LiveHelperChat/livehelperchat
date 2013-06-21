<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Blocked users');?></h1>

<?php if (!empty($items)) : ?>
<table class="twelve" cellpadding="0" cellspacing="0">
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
        <td><?php echo $departament->ip?></td>
        <td><?php echo htmlspecialchars($departament->datets_front)?></td>
        <td><?php echo htmlspecialchars($departament->user)?></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="tiny alert button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>/(remove_block)/<?php echo $departament->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Remove block');?></a></td>
    </tr>
<?php endforeach; ?>
</table>
<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Empty...');?></p>
<?php endif; ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>