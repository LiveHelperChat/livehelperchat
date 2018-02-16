<tr>
    <td colspan="2">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Created at')?> - <?php echo $chat->time_created_front?><?php if ($chat->pnd_time != $chat->time && $chat->pnd_time > 0) : ?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Started at')?> - <?php echo $chat->pnd_time_front?><?php endif; ?><?php if ($chat->cls_time > 0 && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closed at')?> - <?php echo $chat->cls_time_front?><?php endif;?>
    </td>
</tr>