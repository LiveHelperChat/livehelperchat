<div class="" style="width:490px;min-height:260px;position:relative;overflow:hidden;">
<a href="<?php echo htmlspecialchars(trim($online_user->current_page))?>" class="no-wrap fs11"><?php echo htmlspecialchars(trim($online_user->current_page))?></a>

<div class="mt10 section-container auto mb0" data-section >
  <section id="online-user-info">
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></a></p>
    <div class="content" data-section-content>

    <div>

	    <div class="right">
		<p class="fs11">
		<?php if ( !empty($online_user->user_country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $online_user->user_country_code?>.png" alt="<?php echo htmlspecialchars($online_user->user_country_name)?>" title="<?php echo htmlspecialchars($online_user->user_country_name)?>" /><?php endif; ?> (<?php echo $online_user->ip?>)
		<?php if ( !empty($online_user->city) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','City');?>: <?php echo htmlspecialchars($online_user->city) ?><?php endif;?>
		<?php if ( !empty($online_user->lat) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Lat.');?> <?php echo htmlspecialchars($online_user->lat) ?><?php endif;?>
		<?php if ( !empty($online_user->lon) ) :?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Lon.');?> <?php echo htmlspecialchars($online_user->lon) ?><?php endif;?>
		</p>
		</div>

		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?> <?php echo htmlspecialchars($online_user->lastactivity_ago)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?></h5>

		<img src="<?php if ($online_user->operator_message == '') : ?><?php echo erLhcoreClassDesign::design('images/icons/user_inactive.png');?><?php elseif ($online_user->message_seen == 1 && $online_user->operator_message != '') : ?><?php echo erLhcoreClassDesign::design('images/icons/user_green_32.png');?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user.png');?><?php endif;?>" title="<?php if ($online_user->message_seen == 0) : ?><?php if ($online_user->operator_message == '') : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any message from operator');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User have not seen message from operator, or message window still open.');?><?php endif; ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen message from operator.');?><?php endif; ?>" /><img src="<?php echo erLhcoreClassDesign::design('images/icons/browsers.png');?>" title="<?php echo htmlspecialchars($online_user->user_agent)?>" />
		<?php if ($online_user->chat_id > 0) : ?>
		        <img <?php if ($online_user->can_view_chat == true) : ?>class="action-image" onclick="$.colorbox({'iframe':true,height:'500px',width:'500px', href:'<?php echo erLhcoreClassDesign::baseurl('chat/previewchat')?>/<?php echo $online_user->chat_id?>'});"<?php endif;?> src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?>" />
		        <?php else : ?>
		        <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is not having any chat right now');?>" />
		        <?php endif; ?>
		        <?php if ( ($operator_user = $online_user->operator_user) !== false ) : ?>
		    <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32.png');?>" title="<?php echo htmlspecialchars($operator_user); ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has send message to user');?>" />
		    <?php else : ?>
		    <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','No one has send any message to user yet');?>" />
		    <?php endif; ?>

		<ul class="circle fs11">
		    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?> - <?php echo $online_user->first_visit_front?></li>
		    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?> - <?php echo $online_user->last_visit_front?></li>
		    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - <?php echo $online_user->pages_count?></li>
		    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?> - <?php echo $online_user->time_on_site_front?></li>
		</ul>

		<input type="button" class="small button radius mb0" onclick="$.colorbox({'iframe':true,height:'500px',width:'500px', href:'<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $online_user->id?>'});" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"/>

	</div>


    </div>
  </section>
  <?php if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Footprint')?></a></p>
    <div class="content" data-section-content>


      	<div>
		<ul class="foot-print-content circle mb0" style="max-height: 170px;">
		<?php foreach (erLhcoreClassModelChatOnlineUserFootprint::getList(array('filter' => array('online_user_id' => $online_user->id))) as $footprintItems) : ?>
		<li>
		<a target="_blank" href="<?php echo htmlspecialchars($footprintItems->page);?>"><?php echo $footprintItems->time_ago?> | <?php echo htmlspecialchars($footprintItems->page);?></a>
		</li>
		<?php endforeach;?>
		</ul>
		</div>


    </div>
  </section>
  <?php endif;?>
</div>

</div>