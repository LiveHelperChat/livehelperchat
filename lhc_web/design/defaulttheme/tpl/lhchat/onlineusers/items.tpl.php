<table class="table table-sm mb-0 table-small table-fixed" cellpadding="0" cellspacing="0">

<?php foreach ($items as $item) : ?>
    <tr class="online-user-filter-row<?php $item->last_visit_seconds_ago < 15 ? print ' recent-visit' : ''?><?php $item->last_check_time_ago < 293 ? print ' online_user' : ''?>" id="mass-uo-vid-<?php echo htmlspecialchars($item->vid)?>">
        <td style="width: 20px"><input name="receivesNotification[]" id="mass-receiver-check-<?php echo htmlspecialchars($item->vid)?>" class="online-user-filter-row-check" type="checkbox" value="<?php echo $item->id?>"></td>

        <td>
            <input type="hidden" class="mass-vid-list" value="<?php echo htmlspecialchars($item->vid)?>" />
            <div class="btn-group" role="group" aria-label="...">
                <a href="#" class="btn btn-xs btn-outline-secondary" id="mass-ou-face-<?php echo $item->vid?>" <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/face_icon.tpl.php'));?> onclick="window.open(WWW_DIR_JAVASCRIPT + 'chat/getonlineuserinfo/<?php echo $item->id ?>?popup=true','onlineinfo-chat-id-<?php echo $item->id ?>','menubar=1,resizable=1,width=800,height=650');" ><i class="material-icons">info_outline</i><?php echo $item->lastactivity_ago?> | <?php echo htmlspecialchars($item->nick)?>&nbsp;
                    <?php if ($item->user_country_code) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo htmlspecialchars($item->user_country_code)?>.png" alt="<?php echo htmlspecialchars($item->user_country_name)?>" /><?php endif;?>
                </a><?php if ($item->chat_id) : ?><span data-title="<?php echo htmlspecialchars((is_object($item->chat) ? $item->chat->nick : 'Visitor'),ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow(<?php echo $item->chat_id?>,$(this).attr('data-title'))" class="btn btn-xs btn-outline-success action-image" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Preview chat')?>"><i class="material-icons mr0">chat</i></span><?php endif; ?>
                <?php if ($item->total_visits > 1) : ?> <span class="btn btn-xs btn-outline-info"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visits');?> (<?php echo $item->total_visits ?>)</span><?php endif; ?>

                <?php if ($item->total_visits == 1) : ?><span class="btn btn-outline-success btn-xs"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New');?></span><?php endif; ?>

                <?php if ($item->operator_message != "") : ?>
                <span title="<?php echo htmlspecialchars($item->operator_user_string)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" class="btn btn-xs <?php $item->message_seen == 1 ? 'btn-outline-success' : 'btn-outline-danger'?>"><i class="material-icons">chat_bubble_outline</i>
                    <?php if ($item->message_seen == 1) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Seen')?>
                    <?php else : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Unseen')?>
                    <?php endif; ?>
                    </span>
               <?php endif; ?>

            </div>

            <?php if ($item->page_title != '' || $item->current_page != '') : ?>
            <div class="abbr-list">
                <i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?>">&#xE8A0;</i><a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($item->current_page)?>" title="<?php echo htmlspecialchars($item->current_page)?>"><?php echo htmlspecialchars($item->page_title != '' ? $item->page_title : $item->current_page)?></a>
            </div>
            <?php endif; ?>

            <?php if ($item->referrer != '') : ?>
            <div class="abbr-list">
                <i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','From');?>">&#xE8A0;</i><a target="_blank" rel="noopener" href="http:<?php echo htmlspecialchars($item->referrer)?>" title="<?php echo htmlspecialchars($item->referrer)?>"><?php echo htmlspecialchars($item->referrer)?></a>
            </div>
            <?php endif; ?>

        </td>
    </tr>
<?php endforeach; ?>
</table>