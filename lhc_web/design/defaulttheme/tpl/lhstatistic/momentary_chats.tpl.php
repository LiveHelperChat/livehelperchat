<table class="table table-hover table-sm">
    <thead>
    <tr>
        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','ID')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Chat duration')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Started')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Ended')?></th>
    </tr>
    </thead>
    <?php $hideStatusText = true;?>
    <?php foreach ($previousChats as $chat) : ?>
        <tr>
            <td nowrap="nowrap"><a class="material-icons"  data-title="pavlocasino77" onclick="lhinst.startChatNewWindow('1647602253',$(this).attr('data-title'))">open_in_new</a><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_column.tpl.php'));?><?php echo $chat->id?></td>
            <td>
                <?php if ($chat->chat_duration > 0) : ?>
                    <?php echo erLhcoreClassChat::formatSeconds($chat->chat_duration)?>
                <?php endif; ?>
            </td>
            <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->time :  $chat->time/1000);?></td>
            <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->cls_time :  $chat->cls_time/1000);?></td>
        </tr>
    <?php endforeach; ?>
</table>