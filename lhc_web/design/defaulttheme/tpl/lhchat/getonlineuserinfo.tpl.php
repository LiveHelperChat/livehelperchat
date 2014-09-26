<div class="online-user-info">
<a href="<?php echo htmlspecialchars(trim($online_user->current_page))?>" class="no-wrap fs11"><?php echo htmlspecialchars(trim($online_user->referrer))?></a>

<div class="mt10 section-container auto mb0" data-section >
  <section id="online-user-info">
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></a></p>
    <div class="content" data-section-content>

    <div>
    	<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>
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

  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats.tpl.php')); ?>
  
  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/screenshot.tpl.php')); ?>

</div>

</div>