<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation_pre.tpl.php')); ?>
<?php if ($chat_translation_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
    <?php 
    // This values comes from tab template
    if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
    <div role="tabpanel" class="tab-pane<?php if ($chatTabsOrderDefault == 'chat_translation_tab') print ' active';?>" id="main-user-info-translation-<?php echo $chat->id?>">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
            		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Visitor language');?></label> 
            		<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                            'input_name'     => 'chat_locale_'.$chat->id,
            				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatically detected'),
                            'selected_id'    => $chat->chat_locale,
            	            'css_class'      => 'form-control',
                            'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
                    )); ?> 
            	</div>
            </div>
            <div class="col-xs-6">
            	<div class="form-group">
            	       <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','My language');?></label> 
            	       <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                                'input_name'     => 'chat_locale_to_'.$chat->id,
                				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatically detected'),
                                'selected_id'    => $chat->chat_locale_to,
                	            'css_class'      => 'form-control',
                                'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
                        )); ?> 
            	</div>
        	</div>
    	</div>
        <div class="btn-group form-group" role="group" aria-label="...">
            <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Auto translate')?>" class="translate-button-<?php echo $chat->id?> btn btn-default<?php if ($chat->chat_locale != '' && $chat->chat_locale_to != '') :?> btn-success<?php endif;?>" data-loading-text="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translating')?>..." onclick="return lhc.methodCall('lhc.translation','startTranslation',{'btn':$(this),'chat_id':'<?php echo $chat->id?>'})" />
        </div>
    </div>
    <?php endif;?>
<?php endif;?>