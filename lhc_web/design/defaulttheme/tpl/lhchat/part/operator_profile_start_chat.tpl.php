<div class="operator-info float-break<?php if (!isset($start_data_fields['remove_operator_space']) || $start_data_fields['remove_operator_space'] == false) : ?> mb10 round-profile<?php else : ?><?php endif;?>">
	<div class="pull-left pr5">		 
     	<?php if ($theme !== false && $theme->operator_image_url != '') : ?>
     			<img src="<?php echo $theme->operator_image_url?>" alt="" />
     	<?php else : ?>
     		<i class="icon-assistant material-icons">account_box</i>
     	<?php endif;?> 
     </div>
     <div class="pl10">
     	<?php $rightLanguage = true;?>
	 	<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
	    <span><i><?php if ($theme !== false && $theme->intro_operator_text != '') : ?><?php echo htmlspecialchars($theme->intro_operator_text); ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Have a question? Ask us!');?>
	    <?php endif;?>
	    </i></span>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_start_chat_post.tpl.php'));?>
     </div>
</div>
