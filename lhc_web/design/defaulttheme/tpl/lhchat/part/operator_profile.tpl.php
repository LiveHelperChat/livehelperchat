<div class="operator-info float-break">
	<div class="pull-left pr5">
		<?php if ($user->has_photo) : ?>
     			<img src="<?php echo $user->photo_path?>" alt="<?php echo htmlspecialchars($user->name_support)?>" />
     	<?php else : ?>
     		<i class="icon-assistant material-icons">account_box</i>
     	<?php endif;?>
     </div>
     <div class="pl10">        
	    <div><strong><?php echo htmlspecialchars($user->name_support)?></strong></div>
	    <?php if (isset($extraMessage)) : ?>
	    	<i><?php echo $extraMessage;?></i>
	    <?php endif;?>
	    
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_pre.tpl.php'));?>
	    
	    <?php if (!isset($hideThumbs) || $hideThumbs == false) : ?>
     	
     	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/thumbs.tpl.php'));?>
     	
     	<?php if ($user->skype != '') : ?>
     		<a href="skype:<?php echo htmlspecialchars($user->skype)?>?call" class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Skype call'); ?>">phone_in_talk</a>
     	<?php endif;?>
     	
     	<?php endif;?>
     	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_post.tpl.php'));?>
     </div>
</div>