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
    <p class="title" data-section-title><a href="#panel1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?>"><i class="icon-chat chat-pending"></i><span>{{pending_chats.list.length != false && pending_chats.list.length > 0 ? ' ('+pending_chats.list.length+')' : ''}}</span></a></p>
    <div class="content" data-section-content>
      <div>
      	<div id="pending-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?></div>
  	  	<br/>
  	  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
  	  </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($activeTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?>"><i class="icon-chat chat-active"></i><span>{{active_chats.list.length != false && active_chats.list.length > 0 ? ' ('+active_chats.list.length+')' : ''}}</span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="active-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?></div>
     	<br/>
 	 	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($unreadTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel3" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?>"><i class="icon-comment chat-unread"></i><span>{{unread_chats.list.length != false && unread_chats.list.length > 0 ? ' ('+unread_chats.list.length+')' : ''}}</span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="unread-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?></div>
  		<br/>
  		<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>

  <?php if ($closedTabEnabled == true) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel4" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?>"><i class="icon-cancel-circled chat-closed"></i><span>{{closed_chats.list.length != false && closed_chats.list.length > 0 ? ' ('+closed_chats.list.length+')' : ''}}</span></a></p>
    <div class="content" data-section-content>
      <div>
     	<div id="closed-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?></div>
  		<br/>
  		<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
      </div>
    </div>
  </section>
  <?php endif;?>
</div>

<script type="text/javascript">
function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    window.focus();
};

lhinst.attachTabNavigator();

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/opened_chats_js.tpl.php')); ?>
</script>