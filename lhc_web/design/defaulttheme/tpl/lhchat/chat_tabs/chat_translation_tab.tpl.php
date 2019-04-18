<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation_tab_pre.tpl.php')); ?>
<?php if ($chat_translation_tab_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>  
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action_data.tpl.php')); ?>
    <?php if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
    <li class="nav-item" role="presentation"><a class="nav-link <?php if ($chatTabsOrderDefault == 'chat_translation_tab') print ' active';?>" href="#main-user-info-translation-<?php echo $chat->id?>" aria-controls="main-user-info-translation-<?php echo $chat->id?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatic translation')?>" role="tab" data-toggle="tab"><i class="material-icons mr-0">&#xf5ca;</i></a></li>
    <?php endif;?>
<?php endif;?>