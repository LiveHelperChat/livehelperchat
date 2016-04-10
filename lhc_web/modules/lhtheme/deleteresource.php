<?php

$form = erLhAbstractModelAdminTheme::fetch((int)$Params['user_parameters']['id']);
$attributes = $form->{(string)$Params['user_parameters']['context'].'_array'};

$form->removeResource((string)$Params['user_parameters']['context'], (string)$Params['user_parameters']['hash']);

echo json_encode(array('error' => false));
exit;