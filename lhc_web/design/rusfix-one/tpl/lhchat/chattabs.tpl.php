<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>

<?php
$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
?>

<div class="section-container auto" data-section="auto" id="tabs">
  <?php if ($pendingTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?>"><i class="icon-chat chat-pending"></i><span class="pn-cnt"></span></a></p>
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
    <p class="title" data-section-title><a href="#panel2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?>"><i class="icon-chat chat-active"></i><span class="ac-cnt"></span></a></p>
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
    <p class="title" data-section-title><a href="#panel3" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?>"><i class="icon-comment chat-unread"></i><span class="un-cnt"></span></a></p>
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
    <p class="title" data-section-title><a href="#panel4" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?>"><i class="icon-cancel-circled chat-closed"></i><span class="cl-cnt"></span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="closed-chat-list"></div>
  		<br/>
  		<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>
</div>


<script type="text/javascript">
$(function() {
	chatsyncadmininterface();
});

function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    window.focus();
};

lhinst.attachTabNavigator();

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/opened_chats_js.tpl.php')); ?>
</script>