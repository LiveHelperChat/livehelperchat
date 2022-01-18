<div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 pl-2 pr-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Operator chats during this chat')?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body mx550">

            <?php if ($chat->cls_time == 0) : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Present chat was not closed yet. This information will be inaccurate at the moment.')?></p>
            <?php else : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','It shows only chats if they were closed. We show maximum of 10 chats in total.')?></p>
            <?php endif; ?>

            <?php
                $previousChats = array_reverse(erLhcoreClassModelChat::getList(['sort' => 'id DESC', 'limit' => 5, 'filtergt' => ['cls_time' => $chat->time], 'filterlt' => ['id' => $chat->id], 'filter' => ['user_id' => $chat->user_id]]));
                $nextChats = erLhcoreClassModelChat::getList(['sort' => 'id ASC', 'limit' => 5, 'filterlt' => ['time' => $chat->cls_time], 'filtergt' => ['cls_time' => 0, 'id' => $chat->id], 'filter' => ['user_id' => $chat->user_id]]);
            ?>

            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','ID')?></th>
                        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Chat duration')?></th>
                        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Started')?></th>
                        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Ended')?></th>
                    </tr>
                </thead>
                <?php foreach ($previousChats as $previewChat) : ?>
                    <tr>
                        <td nowrap="nowrap"><a class="material-icons" onclick="lhc.previewChat(<?php echo $previewChat->id?>)">info_outline</a><?php echo $previewChat->id?></td>
                        <td><?php echo $previewChat->chat_duration_front?></td>
                        <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$previewChat->time);?></td>
                        <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$previewChat->cls_time);?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="border border-primary bg-light">
                    <td><?php echo $chat->id?></td>
                    <td><?php echo $chat->chat_duration_front?></td>
                    <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);?></td>
                    <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->cls_time);?></td>
                </tr>
                <?php foreach ($nextChats as $previewChat) : ?>
                    <tr>
                        <td><a class="material-icons" onclick="lhc.previewChat(<?php echo $previewChat->id?>)">info_outline</a><?php echo $previewChat->id?></td>
                        <td>
                            <?php if ($previewChat->chat_duration_front !== null) : ?>
                            <?php echo $previewChat->chat_duration_front?>
                            <?php else : ?>
                            -
                            <?php endif; ?>
                        </td>
                        <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$previewChat->time);?></td>
                        <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$previewChat->cls_time);?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>