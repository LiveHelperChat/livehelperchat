<div class="operator-info d-flex">
	<div>
     	<?php if ($theme !== false && $theme->operator_image_url != '') : ?>
     			<img width="48" height="48" src="<?php echo $theme->operator_image_url?>" alt="" />
     	<?php else : ?>
     		<i class="icon-assistant material-icons">
                <?php if (isset($react) && $react == true) : ?>&#xf10d;<?php else : ?>account_box<?php endif; ?>
            </i>
     	<?php endif;?> 
     </div>
     <div class="p-1 pl-2 w-100">

         <?php if (!isset($react)) : ?>
            <?php $rightLanguage = true;?>
            <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
         <?php endif; ?>

	    <span><i><?php if ($theme !== false && $theme->intro_operator_text != '') : ?><?php echo htmlspecialchars($theme->intro_operator_text); ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Have a question? Ask us!');?>
	    <?php endif;?>
	    </i></span>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_start_chat_post.tpl.php'));?>
     </div>
</div>
