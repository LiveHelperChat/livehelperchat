<div class="operator-info d-flex">
	<div>
		<?php if ($user->has_photo) : ?>
     			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_name_support_img.tpl.php'));?>
     	<?php else : ?>
     		<i class="icon-assistant material-icons">account_box</i>
     	<?php endif;?>
     </div>
     <div class="p-1">
	    
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_name_support.tpl.php'));?>

	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_pre.tpl.php'));?>
	    	    	    
	    <?php if (!isset($hideThumbs) || $hideThumbs == false) : ?>
     
        <?php if (!isset($theme) || $theme === false || $theme->show_voting == 1) : ?>
     	  <?php include(erLhcoreClassDesign::designtpl('lhchat/part/thumbs.tpl.php'));?>
     	<?php endif;?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/notifications_subscribe.tpl.php'));?>

     	<?php if ($user->skype != '') : ?>
     		<a href="skype:<?php echo htmlspecialchars($user->skype)?>?call" class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Skype call'); ?>">phone_in_talk</a>
     	<?php endif;?>

     	<?php endif;?>

         <?php if (isset($react) && $react === true && isset($chat) && $chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && isset($theme) && is_numeric($theme->bot_configuration_array['switch_to_human'])) : ?>
         <div id="transfer-to-human-btn" class="pt5 d-inline-block<?php $theme->bot_configuration_array['switch_to_human'] == 0 ? print '' : print ' hide' ?>">
             <a href="#" onclick="return lhinst.transferToHuman(<?php echo $chat->id?>,'<?php echo $chat->hash?>',$(this))" class="btn btn-light btn-sm btn-xs pointer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Switch To Human')?></a>
         </div>
         <?php endif;?>

         <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_post.tpl.php'));?>
     </div>
</div>