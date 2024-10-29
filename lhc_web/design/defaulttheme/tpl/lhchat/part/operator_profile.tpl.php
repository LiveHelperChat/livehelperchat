<div class="operator-info d-flex">
	<div class="align-self-center op-photo">
		<?php if ($user->has_photo_avatar) : ?>
     			<?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_name_support_img.tpl.php'));?>
        <?php elseif (isset($theme) && $theme !== false && $theme->operator_image_avatar !== false) : ?>
                <?php if ($theme->operator_image_url !== false) : ?>
                    <img width="48" height="48" src="<?php echo $theme->operator_image_url?>" alt="">
                <?php else : ?>
                    <img width="48" height="48" src="<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($theme->bot_configuration_array['operator_avatar'])?>" alt="" />
                <?php endif; ?>
     	<?php else : ?>
     		<i class="icon-assistant material-icons me-0"><?php if (isset($react) && $react === true) : ?>&#xf10d;<?php else : ?>account_box<?php endif; ?></i>
     	<?php endif;?>
     </div>
     <div class="p-1 ps-2 operator-profile-content">
	    
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_name_support.tpl.php'));?>

	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_pre.tpl.php'));?>
	    	    	    
	    <?php if (!isset($hideThumbs) || $hideThumbs == false) : ?>
     
        <?php if (!isset($theme) || $theme === false || $theme->show_voting == 1) : ?>
     	  <?php include(erLhcoreClassDesign::designtpl('lhchat/part/thumbs.tpl.php'));?>
     	<?php endif;?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/notifications_subscribe.tpl.php'));?>

     	<?php if ($user->skype != '') : ?>
     		<a href="skype:<?php echo htmlspecialchars($user->skype)?>?call" class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Skype call'); ?>"><?php if (isset($react) && $react === true) : ?>&#xf117;<?php else : ?>phone_in_talk<?php endif; ?></a>
     	<?php endif;?>

     	<?php endif;?>

         <?php if (isset($react) && $react === true && isset($chat) && $chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && isset($theme) && is_object($theme) && isset($theme->bot_configuration_array['switch_to_human']) && is_numeric($theme->bot_configuration_array['switch_to_human'])) : ?>
         <div id="transfer-to-human-btn" class="pt5 d-inline-block<?php $theme->bot_configuration_array['switch_to_human'] == 0 ? print '' : print ' hide' ?>">
             <a href="#" onclick="return lhinst.transferToHuman(<?php echo $chat->id?>,'<?php echo $chat->hash?>',$(this))" class="btn btn-light btn-sm btn-xs pointer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Switch To Human')?></a>
         </div>
         <?php endif;?>

         <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_post.tpl.php'));?>
     </div>
</div>