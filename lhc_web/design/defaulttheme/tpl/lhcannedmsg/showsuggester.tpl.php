<div class="canned-suggester">
    <ul class="list-unstyled canned-list" id="canned-hash-<?php echo $chat->id?>">
        <?php foreach (erLhcoreClassModelCannedMsgTagLink::formatSuggester($keyword,array('chat' => $chat, 'user' => erLhcoreClassUser::instance()->getUserData())) as $item) : ?>
            <li><a href="#">[<?php echo htmlspecialchars($item['tag']->cnt)?>] <?php if ($item['tag']->tag != '') : ?><?php echo htmlspecialchars($item['tag']->tag)?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/buttons','No-Tag');?><?php endif;?> &raquo;</a>
                <ul class="list-unstyled list-sub-items row list-inline me-0 ms-0">
                    <?php
                    $itemsPerPart = 20;
                    $itemsPerPartAdjusted = max(ceil(count($item['messages']) / 3), $itemsPerPart);
                    $parts = array();
                    $parts[] = array_splice($item['messages'],0, $itemsPerPartAdjusted);
                    $parts[] = array_splice($item['messages'], 0 , $itemsPerPartAdjusted);
                    $parts[] = array_splice($item['messages'],0, $itemsPerPartAdjusted);
                    ?>
                    <?php foreach ($parts as $part) : if (!empty($part)) : ?>
                        <li class="col list-inline-item ps-0 pe-0 me-0">
                            <ul class="list-unstyled list-column">
                                <?php foreach ($part as $message) : ?>
                                    <li class="canned-message-item"><span class="me-0 left-return">&laquo;&nbsp;</span><span class="canned-msg" canned_id="<?php echo $message->id?>" <?php if (isset($message->subjects_ids)) : ?>subjects_ids="<?php echo htmlspecialchars(implode(',',$message->subjects_ids))?>"<?php endif;?> data-msg="<?php echo htmlspecialchars($message->msg_to_user)?>"><?php echo htmlspecialchars($message->message_title)?> &raquo;</span></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhcannedmsg/showsuggester_multiinclude.tpl.php'));?>
    </ul>
    <div id="canned-hash-current-<?php echo $chat->id?>" class="current-hash-content"></div>
</div>
