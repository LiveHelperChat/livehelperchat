<?php

$tpl = erLhcoreClassTemplate::getInstance('lhproduct/index.tpl.php');

$attr = erLhcoreClassModelChatConfig::fetch('product_enabled_module');

if ( ezcInputForm::hasPostData() ) {

    $definition = array(
        'product_enabled_moduleValueParam' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    
    $Errors = array();

    if ( $form->hasValidData( 'product_enabled_moduleValueParam' ) && $form->product_enabled_moduleValueParam == true ) {
        $attr->value = 1;
    } else {
        $attr->value = 0;
    }
    
    $attr->saveThis();
    
    $CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache();
}

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Products')));

?>