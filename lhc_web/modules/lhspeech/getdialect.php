<?php 

$language = (int)($Params['user_parameters']['language']);

echo erLhcoreClassRenderHelper::renderCombobox(array(
    'input_name' => 'select_dialect',
    'selected_id' => '',
    'display_name' => 'dialect_name',
    'css_class' => 'form-control',
    'attr_id' => 'lang_code',
    'list_function' => 'erLhcoreClassModelSpeechLanguageDialect::getList',
    'list_function_params' => array('filter' => array('language_id' => $language))
));

exit;
?>