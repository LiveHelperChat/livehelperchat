<?php

$Module = array( "name" => "Captcha module",
				 'variable_params' => true );

$ViewList = array();

$ViewList['captchastring'] = array(
    'params' => array('captcha_name','timets')
);

$ViewList['test'] = array(
    'params' => array()
);

$FunctionList = array();

?>