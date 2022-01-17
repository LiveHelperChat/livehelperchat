<?php

if (isset($_SERVER['HTTP_X_CSRFTOKEN']) && $currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    $form = erLhAbstractModelAdminTheme::fetch((int)$Params['user_parameters']['id']);
    $attributes = $form->{(string)$Params['user_parameters']['context'].'_array'};
    $form->removeResource((string)$Params['user_parameters']['context'], (string)$Params['user_parameters']['hash']);
}

echo json_encode(array('error' => false));
exit;