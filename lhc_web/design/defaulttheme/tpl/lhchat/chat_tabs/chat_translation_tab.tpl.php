<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
    <?php $dataChatTranslation = !isset($dataChatTranslation) ? erLhcoreClassModelChatConfig::fetch('translation_data')->data_value : $dataChatTranslation; ?>
    <?php if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
    <li role="presentation"><a href="#main-user-info-translation-<?php echo $chat->id?>" aria-controls="main-user-info-translation-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatic translation')?>" role="tab" data-toggle="tab"><i class="icon-language"></i></a></li>
    <?php endif;?>
<?php endif;?>