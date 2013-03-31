<?php

$canListOnlineUsers = false;

if (erLhcoreClassModelChatConfig::fetch('list_online_operators')->current_value == 1) {
	$currentUser = erLhcoreClassUser::instance();
	$canListOnlineUsers = $currentUser->hasAccessTo('lhuser','userlistonline');
}

$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);

?>
<dl class="tabs" id="tabs">

  <?php if ($pendingTabEnabled == true) : ?>
  <dd class="active"><a href="#simple1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?><span class="pn-cnt"></span></a></dd>
  <?php endif;?>

  <?php if ($activeTabEnabled == true) : ?>
  <dd><a href="#simple2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?><span class="ac-cnt"></span></a></dd>
  <?php endif;?>

  <?php if ($unreadTabEnabled == true) : ?>
  <dd><a href="#simple3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?><span class="un-cnt"></span></a></dd>
  <?php endif;?>

  <?php if ($closedTabEnabled == true) : ?>
  <dd><a href="#simple4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?><span class="cl-cnt"></span></a></dd>
  <?php endif;?>

  <?php if ($canListOnlineUsers == true) : ?>
  <dd><a href="#simple5"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Online operators');?><span class="onp-cnt"></span></a></dd>
  <?php endif; ?>

</dl>
<ul class="tabs-content" id="tabs-content">

  <?php if ($pendingTabEnabled == true) : ?>
  <li class="active" id="simple1Tab">
  	<div id="pending-chat-list"></div>
  	<br/>
  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
  </li>
  <?php endif;?>

  <?php if ($activeTabEnabled == true) : ?>
  <li id="simple2Tab">
  	<div id="active-chat-list"></div>
    <br/>
 	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
  </li>
  <?php endif;?>

  <?php if ($unreadTabEnabled == true) : ?>
  <li id="simple3Tab">
  	<div id="unread-chat-list"></div>
  	<br/>
  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
  </li>
  <?php endif;?>

  <?php if ($closedTabEnabled == true) : ?>
  <li id="simple4Tab">
  	<div id="closed-chat-list"></div>
  	<br/>
  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
  </li>
  <?php endif;?>

  <?php if ($canListOnlineUsers == true) : ?>
  <li id="simple5Tab">
  	<div id="online-operator-list"></div>
  </li>
  <?php endif; ?>

</ul>
