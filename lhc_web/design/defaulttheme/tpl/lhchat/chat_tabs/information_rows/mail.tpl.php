<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','closerelated')) : ?>
<tr>
    <td colspan="2" >
        <h6 class="fw-bold action-image" onclick="lhinst.explandCollapse('mailchat',<?php echo $chat->id?>,'mailconv/relatedtickets/<?php echo $chat->id?>');" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Collapse/Expand')?>">
            <i class="material-icons">mail</i>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Related mail tickets')?>
            <i class="material-icons" data-loaded="false" id="expand-action-mailchat-<?php echo $chat->id?>">expand_more</i>
        </h6>
        <div id="lhc-list-mailchat-<?php echo $chat->id?>" style="display: none"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Fetching related mails...')?></div>
    </td>
</tr>
<?php endif; ?>