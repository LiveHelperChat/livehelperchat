<div class="pull-right">
<p class="fs12">
<?php if ( !empty($online_user->user_country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $online_user->user_country_code?>.png" alt="<?php echo htmlspecialchars($online_user->user_country_name)?>" title="<?php echo htmlspecialchars($online_user->user_country_name)?>" /><?php endif; ?> (<?php echo htmlspecialchars($online_user->ip)?>)
<?php if ( !empty($online_user->city) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','City');?>: <?php echo htmlspecialchars($online_user->city) ?><?php endif;?>
<?php if ( !empty($online_user->lat) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Lat.');?> <?php echo htmlspecialchars($online_user->lat) ?><?php endif;?>
<?php if ( !empty($online_user->lon) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Lon.');?> <?php echo htmlspecialchars($online_user->lon) ?><?php endif;?>
<?php if ( !empty($online_user->visitor_tz) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time zone');?>: <?php echo htmlspecialchars($online_user->visitor_tz),' ',$online_user->visitor_tz_time ?><?php endif;?>


<?php if (!empty($online_user->identifier)) : ?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Identifier');?> - <?php echo htmlspecialchars($online_user->identifier)?><?php endif;?>
</p>

<?php if ($online_user->online_attr != '') : ?>
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Additional information')?></h5>
<pre>
<?php echo htmlspecialchars(json_encode(json_decode($online_user->online_attr),JSON_PRETTY_PRINT));?>
</pre>
<?php endif;?>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/parts/top_user_info.tpl.php')); ?>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?> <?php echo htmlspecialchars($online_user->lastactivity_ago)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?><?php $timeoutOnPage = (int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value; if ($timeoutOnPage > 0) : ?>,<br/><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','On page');?> - <?php if ($online_user->last_check_time_ago < ($timeoutOnPage+3)) : ?><i class="icon-user-status material-icons icon-user-online" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Yes')?>">face</i><?php else : ?><i class="icon-user-status material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','No')?>">face</i><?php endif;?></b><?php endif;?></h5>

<ul class="list-unstyled">
    <li><i class="material-icons">face</i><?php if ($online_user->message_seen == 0) : ?><?php if ($online_user->operator_message == '') : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any message from operator');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User have not seen message from operator, or message window still open.');?><?php endif; ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen message from operator.');?><?php endif; ?></li>
    <li>
    <?php if ($online_user->chat_id > 0) : ?>
        <?php if ($online_user->can_view_chat == true) : ?><a href="#" onclick="return lhc.previewChat('<?php echo $online_user->chat_id?>');"><?php endif;?><i class="material-icons chat-active">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?><?php if ($online_user->can_view_chat == true) : ?></a><?php endif;?>
    <?php else : ?>
    <i class="material-icons">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is not having any chat right now');?>
    <?php endif; ?>
    </li>
    <li><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Operator')?>" class="material-icons">account_box</i>
    <?php if ( ($operator_user = $online_user->operator_user) !== false ) : ?>
    <?php echo htmlspecialchars($operator_user); ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has send message to user');?>
    <?php else : ?>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','No one has send any message to user yet');?>
    <?php endif; ?>
    </li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','First visit');?> - <?php echo $online_user->first_visit_front?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last visit');?> - <?php echo $online_user->last_visit_front?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Total visits');?> - <?php echo $online_user->total_visits?></li>
    <li><?php echo $online_user->invitation_count?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','time(s) invitation logic was applied');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - <?php echo $online_user->pages_count?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Total pageviews');?> - <?php echo $online_user->tt_pages_count?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?> - <?php echo $online_user->time_on_site_front?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Total time on site');?> - <?php echo $online_user->tt_time_on_site_front?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Current page');?> - <a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($online_user->current_page)?>" title="<?php echo htmlspecialchars($online_user->current_page)?>"><?php echo erLhcoreClassDesign::shrt($online_user->current_page,100,100);?></a></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Came from');?> - <a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($online_user->referrer)?>"><?php echo htmlspecialchars($online_user->referrer)?></a></li>
    <li class="fs11"><i class="material-icons">language</i><?php echo htmlspecialchars($online_user->user_agent)?></li>
</ul>