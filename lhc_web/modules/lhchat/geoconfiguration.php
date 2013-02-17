<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/geoconfiguration.tpl.php');

$geoData = erLhcoreClassModelChatConfig::fetch('geo_data');
$data = (array)$geoData->data;

if ( isset($_POST['StoreGeoIPConfiguration']) ) {
    
    $definition = array(
        'UseGeoIP' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'GeoDetectionEnabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'ServerVariableGEOIP_COUNTRY_CODE' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'ServerVariableGEOIP_COUNTRY_NAME' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),       
        'locatorhqAPIKey' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'locatorhqUsername' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        )        
    );

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
        
    if ( $form->hasValidData( 'GeoDetectionEnabled' ) && $form->GeoDetectionEnabled == true ) {        
        $data['geo_detection_enabled'] = 1;
    } else {
        $data['geo_detection_enabled'] = 0;
    }

    if ($data['geo_detection_enabled'] == 1) {    
        if ( $form->hasValidData( 'UseGeoIP' ) ) {
            
            if ($form->UseGeoIP == 'mod_geoip2'){
                
                $data['geo_service_identifier'] = 'mod_geoip2';
                
                if ( $form->hasValidData( 'ServerVariableGEOIP_COUNTRY_CODE' ) && $form->ServerVariableGEOIP_COUNTRY_CODE != '' && isset($_SERVER[$form->ServerVariableGEOIP_COUNTRY_CODE]) ) {
                    $data['mod_geo_ip_country_code'] = $form->ServerVariableGEOIP_COUNTRY_CODE;
                } else {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Country code variable does not exists!');
                }
                
                if ( $form->hasValidData( 'ServerVariableGEOIP_COUNTRY_NAME' ) && $form->ServerVariableGEOIP_COUNTRY_NAME != '' && isset($_SERVER[$form->ServerVariableGEOIP_COUNTRY_NAME]) ) {
                    $data['mod_geo_ip_country_name'] = $form->ServerVariableGEOIP_COUNTRY_NAME;
                } else {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Country name variable does not exists!');
                }
            } elseif ($form->UseGeoIP == 'freegeoip') {
                $data['geo_service_identifier'] = 'freegeoip';
                $responseDetection = erLhcoreClassModelChatOnlineUser::getUserData('freegeoip',$_SERVER['SERVER_ADDR']);
                if ( $responseDetection == false || !isset($responseDetection->country_code) || !isset($responseDetection->country_name) ) {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Setting service provider failed, please check that your service provider allows to make requests to remote pages!');
                }
            } elseif ($form->UseGeoIP == 'locatorhq') {
                $data['geo_service_identifier'] = 'locatorhq';
                
                $filledAPIData = true;
                
                if ( $form->hasValidData( 'locatorhqAPIKey' ) && $form->locatorhqAPIKey != '' ) {
                    $data['locatorhq_api_key'] = $form->locatorhqAPIKey;
                } else {
                    $filledAPIData = false;
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Please enter API key!');
                }
                
                if ( $form->hasValidData( 'locatorhqUsername' ) && $form->locatorhqUsername != '' ) {
                    $data['locatorhqusername'] = $form->locatorhqUsername;
                } else {
                    $filledAPIData = false;
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Please enter API username!');
                }
                
                if ($filledAPIData == true) {
                    $responseDetection = erLhcoreClassModelChatOnlineUser::getUserData('locatorhq',$_SERVER['SERVER_ADDR'],array('username' => $data['locatorhqusername'], 'api_key' => $data['locatorhq_api_key']));
                    if ($responseDetection == false || !isset($responseDetection->country_code)){
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Setting service provider failed, please check that your service provider allows to make requests to remote pages and your API key and username is correct!');
                    }
                }                
            }

        } else {  
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Please choose service provider!');
        }
    }
        
    if (count($Errors) == 0) {   
        $geoData->value = serialize($data);
        $geoData->saveThis();
        $tpl->set('updated','done');         
    }  else {
        $tpl->set('errors',$Errors);
    }
    
}

$tpl->set('geo_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('chat/onlineusers'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online users')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration')));
    

?>