<div class="operator-info float-break">
	<div class="pull-left pr5">
		<?php if ($user->has_photo) : ?>
     			<img src="<?php echo $user->photo_path?>" alt="<?php echo htmlspecialchars($user->name_support)?>" />
     	<?php else : ?>
     		<i class="icon-user icon-assistant"></i>
     	<?php endif;?>
     </div>
     <div class="pl10">
	    <span><strong><?php echo htmlspecialchars($user->name_support)?></strong></span>
	    <?php if (isset($extraMessage)) : ?>
	    	<i><?php echo $extraMessage;?></i>
	    <?php endif;?>
	    <?php if (!isset($hideThumbs) || $hideThumbs == false) : ?>
     	<i class="icon-thumbs-up<?php if ($chat->fbst == 1) : ?> up-voted<?php endif;?>" data-id="1" onclick="lhinst.voteAction($(this))" ></i>
     	<i class="icon-thumbs-down<?php if ($chat->fbst == 2) : ?> down-voted<?php endif;?>" data-id="2" onclick="lhinst.voteAction($(this))"></i>
     	
     	<?php if ($user->skype != '') : ?>
     		<a href="skype:<?php echo htmlspecialchars($user->skype)?>?call" class="icon-skype" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/part/operator_profile','Skype call'); ?>"></a>
     	<?php endif;?>
     	
     	<?php endif;?>
     </div>
</div>