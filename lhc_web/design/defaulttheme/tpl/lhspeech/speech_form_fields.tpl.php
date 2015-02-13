<div class="row">
    <div class="col-md-6">
    	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Language')?></label>
            <?php   
            
            $params = array(
                'input_name' => 'select_language',
                'on_change'  => 'lhinst.getDialect($(this))',
                'selected_id' => $dataSpeech['language'],
                'css_class' => 'form-control',
                'list_function' => 'erLhcoreClassModelSpeechLanguage::getList'
            );
            
            if (isset($dataSpeech['optional'])){
                $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('speech/speech','Use application default recognition language');
            };
                   
            echo erLhcoreClassRenderHelper::renderCombobox($params);
            ?> 
    </div>
    <div class="col-md-6">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Dialect')?></label>
         <?php                       
            $params = array(
                'input_name' => 'select_dialect',
                'attr_id' => 'lang_code',
                'css_class' => 'form-control',
                'display_name' => 'dialect_name',
                'selected_id' => $dataSpeech['dialect'],
                'list_function' => 'erLhcoreClassModelSpeechLanguageDialect::getList',
                'list_function_params' => array('filter' => array('language_id' => $dataSpeech['language']))
            );
            
            if (isset($dataSpeech['optional'])){
                $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('speech/speech','Use application default recognition dialect');
            };
            
            echo erLhcoreClassRenderHelper::renderCombobox($params);
         ?>    	
    </div>
</div>