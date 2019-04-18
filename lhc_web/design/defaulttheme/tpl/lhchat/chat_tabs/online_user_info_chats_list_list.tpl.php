<ul class="foot-print-content list-unstyled" style="max-height: 170px;">
    <?php $hasPrevChat = false; foreach (erLhcoreClassChat::getList(array('limit' => 100, 'filter' => array('online_user_id' => $online_user->id))) as $chatPrev) : ?>
        <?php if (!isset($chat) || $chat->id != $chatPrev->id) : $hasPrevChat = true;?>
            <li>
                <?php if ( !empty($chatPrev->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chatPrev->country_code?>.png" alt="<?php echo htmlspecialchars($chatPrev->country_name)?>" title="<?php echo htmlspecialchars($chatPrev->country_name)?>" />&nbsp;<?php endif; ?>
                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="material-icons" onclick="lhinst.startChatNewWindow('<?php echo $chatPrev->id;?>',$(this).attr('data-title'))" data-title="<?php echo htmlspecialchars($chatPrev->nick,ENT_QUOTES);?>">&#xf3cc;</a><?php echo $chatPrev->id;?>. <?php echo htmlspecialchars($chatPrev->nick);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chatPrev->time);?>) (<?php echo htmlspecialchars($chatPrev->department);?>)
            </li>
        <?php endif; ?>
    <?php endforeach;?>
</ul>

<?php if ($hasPrevChat == false) : ?>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','No previous chats');?>
<?php endif;?>