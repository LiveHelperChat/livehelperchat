<?php if ($is_ajax == false) : ?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online users');?>
    
    <ul class="button-group radius right">
      <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="round button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
      <li><a class="round button small alert" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(clear_list)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Clear list');?></a></li>
    </ul>
    
</h1>

<?php if($tracking_enabled == false) : ?> 
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User tracking is disabled, enable it at');?>&nbsp;-&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/track_online_visitors"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat configuration');?></a></p>
<?php endif; ?>

<div id="online-users">
<?php endif; ?>

<?php if (!empty($items)) : ?>
<table class="twelve online-users-table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th width="17%" nowrap><img src="<?php echo erLhcoreClassDesign::design('images/icons/clock.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?>" /></th>
    <th width="80%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></th>  
    <th width="1%" nowrap><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Status');?></th>  
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Action');?></th>

</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td><?php echo $departament->id?></td>
       
        <td><?php echo htmlspecialchars($departament->lastactivity_ago)?> ago</td>
        <td><?php echo htmlspecialchars($departament->current_page)?></td>
        <td>             
        <div style="width:180px">
        <?php if ( !empty($departament->user_country_code) ) : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $departament->user_country_code?>.png" alt="<?php echo htmlspecialchars($departament->user_country_name)?>" title="<?php echo htmlspecialchars($departament->user_country_name)?>" />
        <?php endif; ?>
                  
        <img src="<?php if ($departament->operator_message == '') : ?><?php echo erLhcoreClassDesign::design('images/icons/user_inactive.png');?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user.png');?><?php endif;?>" title="<?php if ($departament->message_seen == 0) : ?><?php if ($departament->operator_message == '') : ?>User does not have any message from operator<?php else : ?>User have not seen message from operator, or message window still open.<?php endif; ?><?php else : ?>User has seen message from operator.<?php endif; ?>" />
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/browsers.png');?>" title="<?php echo htmlspecialchars($departament->user_agent)?>" />
        
        <?php if ($departament->chat_id > 0) : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment.png');?>" title="User is chatting" />
        <?php else : ?><img src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment_inactive.png');?>" title="User is not having any chat right now" /><?php endif; ?>
        
        <?php if ( ($operator_user = $departament->operator_user) !== false ) : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32.png');?>" title="<?php echo htmlspecialchars($operator_user); ?> has send message to user" />
        <?php else : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32_inactive.png');?>" title="No one has send any message to user yet" />
        <?php endif; ?>  
        
        <img src="<?php echo erLhcoreClassDesign::design('images/icons/ip.png');?>" title="<?php echo $departament->ip?>" />
    
        </div>
        
        </td>   
       
        <td nowrap><input type="button" class="tiny button radius" onclick="$.colorbox({'iframe':true,height:'500px',width:'500px', href:'<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $departament->id?>'});" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Send message');?>"/></td>
    </tr>
<?php endforeach; ?>
</table>
<?php else : ?>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Empty...');?>
<?php endif; ?>

<?php if ($is_ajax == false) : ?>
</div>
<script>startOnlineSync();</script>
<?php endif; ?>