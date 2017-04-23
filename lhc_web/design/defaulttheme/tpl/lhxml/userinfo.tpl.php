<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','ID');?>: <?php echo $onlineUsers->id; ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','IP address');?>: <?php echo htmlspecialchars($onlineUsers->ip); ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','Current page');?>: <?php echo $onlineUsers->current_page; ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','Last visit');?>: <?php echo date(erLhcoreClassModule::$dateDateHourFormat, $onlineUsers->last_visit); ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','User agent');?>: <?php echo $onlineUsers->user_agent; ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','Country code');?>: <?php echo $onlineUsers->user_country_code; ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','Country name');?>: <?php echo $onlineUsers->user_country_name; ?>
<br>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhxml/userinfo','Message seen');?>: <?php echo $onlineUsers->message_seen; ?>