<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/listarchivechats','Archived chats');?> (<?php echo htmlspecialchars($archive->range_from_front)?> - <?php echo htmlspecialchars($archive->range_to_front)?>)</h1>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel.tpl.php')); ?>

<?php if ($pages->items_total > 0) { ?>

<table class="table" cellpadding="0" cellspacing="0" width="100%">
<thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/listarchivechats','Information');?></th>
    </tr>
</thead>
    <?php foreach ($items as $chat) : ?>
    <tr>
        <td><?php echo $chat->id?></td>
        <td>
           <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>
           <a class="csfr-required" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/deletearchivechat')?>/<?php echo $archive->id?>/<?php echo $chat->id?>"><img class="action-image" align="absmiddle" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>"></a> <a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/viewarchivedchat')?>/<?php echo $archive->id?>/<?php echo $chat->id?>"><?php echo $chat->id;?>. <?php echo htmlspecialchars($chat->nick);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);?>) (<?php echo $chat->department;?>)</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Empty...');?></p>
<?php } ?>
