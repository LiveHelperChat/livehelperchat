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

<div class="section-container auto" data-section="auto" id="tabs">
  <?php if ($pendingTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?><span class="pn-cnt"></span></a></p>
    <div class="content" data-section-content>
      <div>
      	<div id="pending-chat-list"></div>
  	  	<br/>
  	  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
  	  </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($activeTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?><span class="ac-cnt"></span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="active-chat-list"></div>
     	<br/>
 	 	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($unreadTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?><span class="un-cnt"></span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="unread-chat-list"></div>
  		<br/>
  		<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($closedTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?><span class="cl-cnt"></span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="closed-chat-list"></div>
  		<br/>
  		<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($canListOnlineUsers == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Online operators');?><span class="onp-cnt"></span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="online-operator-list"></div>
      </div>
    </div>
  </section>
  <?php endif; ?>
</div>






