<?php

$tpl = erLhcoreClassTemplate::getInstance('lhaudit/configuration.tpl.php');

$auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
$data = (array)$auditOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'days_log' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'log_objects' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'days_log' )) {
        $data['days_log'] = $form->days_log ;
    } else {
        $data['days_log'] = 90;
    }

    if ( $form->hasValidData( 'log_objects' )) {
        $data['log_objects'] = $form->log_objects ;
    } else {
        $data['log_objects'] = array();
    }

    $auditOptions->explain = '';
    $auditOptions->type = 0;
    $auditOptions->hidden = 1;
    $auditOptions->identifier = 'audit_configuration';
    $auditOptions->value = serialize($data);
    $auditOptions->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('audit_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options', 'Options')
    )
);

?>