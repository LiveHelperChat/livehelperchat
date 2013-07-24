<?php $currentUser = erLhcoreClassUser::instance(); if ($is_ajax == false) : ?>

<ul class="button-group round geo-settings">
      <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
      <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
      <?php endif; ?>

      <?php if ($currentUser->hasAccessTo('lhchat','allowclearonlinelist')) : ?>
      <li><a class="small button alert" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(clear_list)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Clear list');?></a></li>
      <?php endif; ?>

</ul>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors');?></h1>

<?php if($tracking_enabled == false) : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User tracking is disabled, enable it at');?>&nbsp;-&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/track_online_visitors"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat configuration');?></a></p>
<?php endif; ?>


<div class="section-container auto" data-section="auto">
	  <section>
	    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','List');?></a></p>
	    <div class="content" data-section-content>




<div id="online-users">
<?php endif; ?>

<?php if (!empty($items)) : ?>
<table class="twelve online-users-table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th width="17%" nowrap><img src="<?php echo erLhcoreClassDesign::design('images/icons/clock.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?>" /></th>
    <th width="17%" nowrap><img src="<?php echo erLhcoreClassDesign::design('images/icons/clock.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?>" /></th>
    <th width="80%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></th>
    <th width="1%" nowrap><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Status');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Action');?></th>
    <th width="1%"></th>

</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td><?php echo $departament->id?></td>
        <td nowrap><?php echo htmlspecialchars($departament->lastactivity_ago)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?></td>
        <td><?php echo $departament->time_on_site_front?></td>
        <td><div class="page-url"><span><?php echo htmlspecialchars($departament->current_page)?></span></div></td>
        <td>
        <div style="width:230px">
        <?php if ( !empty($departament->user_country_code) ) : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $departament->user_country_code?>.png" alt="<?php echo htmlspecialchars($departament->user_country_name)?>" title="<?php echo htmlspecialchars($departament->user_country_name)?> | <?php echo htmlspecialchars($departament->city)?>" />
        <?php endif; ?>

        <img src="<?php if ($departament->operator_message == '') : ?><?php echo erLhcoreClassDesign::design('images/icons/user_inactive.png');?><?php elseif ($departament->message_seen == 1 && $departament->operator_message != '') : ?><?php echo erLhcoreClassDesign::design('images/icons/user_green_32.png');?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user.png');?><?php endif;?>" title="<?php if ($departament->message_seen == 0) : ?><?php if ($departament->operator_message == '') : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has not seen a message from the operator, or the message window is still open.');?><?php endif; ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.');?><?php endif; ?>" />

        <img src="<?php echo erLhcoreClassDesign::design('images/icons/browsers.png');?>" title="<?php echo htmlspecialchars($departament->user_agent)?>" />

        <?php if ($departament->chat_id > 0) : ?>
        <img <?php if ($departament->can_view_chat == true) : ?>class="action-image" onclick="$.colorbox({'iframe':true,height:'500px',width:'500px', href:'<?php echo erLhcoreClassDesign::baseurl('chat/previewchat')?>/<?php echo $departament->chat_id?>'});"<?php endif;?> src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?>" />
        <?php else : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is not having any chat right now');?>" /><?php endif; ?>

        <?php if ( ($operator_user = $departament->operator_user) !== false ) : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32.png');?>" title="<?php echo htmlspecialchars($operator_user); ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" />
        <?php else : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','No one has sent a message to the user yet');?>" />
        <?php endif; ?>

        <img src="<?php echo erLhcoreClassDesign::design('images/icons/ip.png');?>" title="<?php echo $departament->ip?>" />

        <img src="<?php echo erLhcoreClassDesign::design('images/icons/information.png');?>" title="<?php echo $departament->first_visit_front?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?><?php echo "\n";?><?php echo $departament->last_visit_front?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?><?php echo "\n"?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - <?php echo $departament->pages_count?><?php if (!empty($departament->identifier)) : echo "\n"?>Identifier - <?php echo htmlspecialchars($departament->identifier)?><?php endif;?>" />



        </div>

        </td>

        <td nowrap><input type="button" class="small button round" onclick="$.colorbox({'iframe':true,height:'500px',width:'500px', href:'<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $departament->id?>'});" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"/></td>
        <td nowrap><a class="small alert button round" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/list','Are you sure?')?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(deletevisitor)/<?php echo $departament->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a></td>

    </tr>
<?php endforeach; ?>
</table>
<?php else : ?>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Empty...');?>
<?php endif; ?>

<?php if ($is_ajax == false) : ?>
</div>
</div>
	  </section>
	  <section>
	    <p class="title" data-section-title><a id="map-activator" href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Map');?></a></p>
	    <div class="content" data-section-content>

				<div class="row">
					<div class="columns large-3">
						<img data-tooltip class="tip-right" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?>" src="<?php echo erLhcoreClassDesign::design('images/icons/home-chat.png')?>" />
	    				<img data-tooltip class="tip-right" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any message from operator');?>" src="<?php echo erLhcoreClassDesign::design('images/icons/home-unsend.png')?>" />
	    				<img data-tooltip class="tip-right" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has message from operator');?>" src="<?php echo erLhcoreClassDesign::design('images/icons/home-send.png')?>" />
					</div>
					<div class="columns large-5"><label for="markerTimeout" class="inline"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Marker timeout before it dissapears from map');?></label></div>
					<div class="columns large-4">
						<select id="markerTimeout">
			    			<option value="30">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>
			    			<option value="60">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minit');?></option>
			    			<option value="120">2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
			    			<option value="300">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
			    			<option value="600">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    			</select>
	    			</div>
				</div>

			<div id="map_canvas" style="height:600px;width:100%;"></div>
		    <script src="https://maps-api-ssl.google.com/maps/api/js?v=3&sensor=false&callback=gMapsCallback"></script>

	    </div>
	  </section>
</div>


<script>startOnlineSync();</script>
<?php endif; ?>