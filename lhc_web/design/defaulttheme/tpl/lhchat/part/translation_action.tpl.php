<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_pre.tpl.php')); ?>
<?php if ($translation_action_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_data.tpl.php')); ?>
	<?php if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
	     <a href="#" class="w-100 btn btn-outline-secondary translate-button-<?php echo $chat->id?><?php if ($chat->chat_locale != '' && $chat->chat_locale_to != '') :?> btn-success<?php endif;?> " title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Auto translate')?>" id="start-trans-btn-<?php echo $chat->id?>" onclick="return lhc.methodCall('lhc.translation','startTranslation',{'btn':$(this),'chat_id':'<?php echo $chat->id?>'})">
             <i class="material-icons mr-0">language</i>
         </a>
	<?php endif;?>
<?php endif;?>