<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
	<?php $dataChatTranslation = !isset($dataChatTranslation) ? erLhcoreClassModelChatConfig::fetch('translation_data')->data_value : $dataChatTranslation; ?>
	<?php if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
	     <a class="btn btn-default translate-button-<?php echo $chat->id?><?php if ($chat->chat_locale != '' && $chat->chat_locale_to != '') :?> btn-success<?php endif;?> icon-language" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Auto translate')?>" onclick="return lhc.methodCall('lhc.translation','startTranslation',{'btn':$(this),'chat_id':'<?php echo $chat->id?>'})"></a>
	<?php endif;?>
<?php endif;?>