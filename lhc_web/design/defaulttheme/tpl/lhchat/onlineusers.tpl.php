<?php if ($is_ajax == false) : ?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online users');?>
    
    <ul class="button-group radius right">
      <li><a class="round button small alert" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(clear_list)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Clear list');?></a></li>
      <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="round button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
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
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','IP');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User agent');?></th>    
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Location');?></th>    
</tr>
</thead>
<?php foreach ($items as $departament) : ?>
    <tr>
        <td><?php echo $departament->id?></td>
        <td><?php echo $departament->ip?></td>
        <td><?php echo htmlspecialchars($departament->lastactivity_ago)?> ago</td>
        <td><?php echo htmlspecialchars($departament->current_page)?></td>
        <td><?php echo htmlspecialchars($departament->user_agent)?></td>        
        <td>
        <?php if ( !empty($departament->user_country_code) ) : ?>
        <img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $departament->user_country_code?>.png" alt="<?php echo htmlspecialchars($departament->user_country_name)?>" title="<?php echo htmlspecialchars($departament->user_country_name)?>" />
        <?php endif; ?>
        </td>        
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