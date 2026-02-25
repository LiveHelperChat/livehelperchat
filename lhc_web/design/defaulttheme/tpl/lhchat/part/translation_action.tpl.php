<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_pre.tpl.php')); ?>
<?php if ($translation_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_data.tpl.php')); ?>
	<?php if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>

		 <?php if (!isset($dataChatTranslation['hide_translate_button']) || $dataChatTranslation['hide_translate_button'] == false) : ?>
	     <a href="#" class="w-100 btn btn-outline-secondary translate-button-<?php echo $chat->id?><?php if ($chat->chat_locale != '' && $chat->chat_locale_to != '' && isset($chat->chat_variables_array['lhc_live_trans'])) :?> btn-outline-success<?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translate operator message to visitor language')?>" id="start-trans-btn-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.translation','translateMessage',{'btn':$(this),'chat_id':'<?php echo $chat->id?>'})">
             <i class="material-icons me-0">language</i>
         </a>
		 <?php endif; ?>

		 <?php if (isset($dataChatTranslation['show_auto_translate']) && $dataChatTranslation['show_auto_translate'] == true) : ?>
	     <a href="#" class="w-100 btn btn-outline-secondary translate-button-<?php echo $chat->id?><?php if ($chat->chat_locale != '' && $chat->chat_locale_to != '' && isset($chat->chat_variables_array['lhc_live_trans'])) :?> btn-outline-success<?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Start translated chat session')?>" id="translate-auto-start-btn-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.translation','startAutoTranslation', {'old' : <?php (isset($dataChatTranslation['translate_old_msg']) && $dataChatTranslation['translate_old_msg'] == true ? print 'true' : print 'false');?>, 'btn':$(this),'chat_id':'<?php echo $chat->id?>'})">
             <i class="material-icons me-0">translate</i>
         </a>
		 <?php endif; ?>

	<?php endif;?>
<?php endif;?>