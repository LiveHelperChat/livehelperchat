<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/cannedmsg.tpl.php'));?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title/Message');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','User');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Auto send');?></th>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_column_multiinclude.tpl.php'));?>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><?php echo nl2br(htmlspecialchars($item->title != '' ? $item->title : $item->msg))?></td>
        <td><?php if ($item->department !== false) : ?><?php echo htmlspecialchars($item->department)?><?php else : ?>-<?php endif;?></td>
        <td><?php echo htmlspecialchars($item->user)?></td>
        <td><?php echo $item->delay?></td>
        <td><?php echo $item->position?></td>
        <td><?php echo $item->auto_send?></td>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_column_content_multiinclude.tpl.php'));?>
        <td nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsgedit')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit message');?></a></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>/(action)/delete/(id)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete message');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('chat/newcannedmsg')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message');?></a>