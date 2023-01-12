<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_pre.tpl.php')); ?>
<?php if ($translation_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_data.tpl.php')); ?>
	<?php if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
	     <a href="#" class="w-100 btn btn-outline-secondary translate-button-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translate operator message to visitor language')?>" id="start-trans-btn-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.translation','translateMessage',{'btn':$(this),'chat_id':'<?php echo $chat->id?>'})">
             <i class="material-icons me-0">language</i>
         </a>
	<?php endif;?>
<?php endif;?>