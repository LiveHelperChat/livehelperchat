<?php

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);

erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['user_id']);

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($search->params_array['input_form']);

erLhcoreClassModule::redirect('chat/list', '/(view)/'.$search->id.$append);
exit;

?>