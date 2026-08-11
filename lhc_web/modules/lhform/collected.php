<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/collected.tpl.php');

$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'form','module_file' => 'collected', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'form','module_file' => 'collected', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$filter = $filterParams['filter'];
$filter['filter']['form_id'] = $form->id;

$departmentIds = $filterParams['input_form']->department_ids;
$userIds = $filterParams['input_form']->user_ids;

$needsJoin = !empty($departmentIds) || !empty($userIds);

if ($filterParams['input_form']->chat_time === true) {
	$needsJoin = true;

	if (isset($filter['filtergte']['ctime'])) {
		$filter['filtergte']['`lh_chat`.`time`'] = $filter['filtergte']['ctime'];
		unset($filter['filtergte']['ctime']);
	}

	if (isset($filter['filterlte']['ctime'])) {
		$filter['filterlte']['`lh_chat`.`time`'] = $filter['filterlte']['ctime'];
		unset($filter['filterlte']['ctime']);
	}
}

if ($needsJoin) {
    $filter['leftjoin']['lh_chat'] = array('lh_chat.id', 'lh_abstract_form_collected.chat_id');
}

if (is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['action'] == 'delete'){

	// Delete selected canned message
	if ($currentUser->hasAccessTo('lhform', 'delete_collected')) {
		try {
			if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
				die('Invalid CSRF Token');
				exit;
			}
			$collected = erLhAbstractModelFormCollected::fetch((int)$Params['user_parameters_unordered']['id']);

			// Clear related chat attributes set by form definition
			if ($collected->chat_id > 0) {
			    $chat = erLhcoreClassModelChat::fetch($collected->chat_id);
			    if ($chat instanceof erLhcoreClassModelChat) {
			        $contentCollected = json_decode($collected->content, true);
			        if (is_array($contentCollected)) {
			            $chatUpdates = [];
			            $chatVariables = $chat->chat_variables_array;
			            $chatVariablesChanged = false;
			            $additionalData = $chat->additional_data_array;
			            $additionalDataChanged = false;

			            foreach ($contentCollected as $params) {
			                if (isset($params['definition']['chat_attr'])) {
			                    $path = explode('.', $params['definition']['chat_attr']);
			                    if ($path[0] == 'chat' && isset($path[1])) {
			                        $chat->{$path[1]} = '';
			                        $chatUpdates[] = $path[1];
			                    } elseif ($path[0] == 'chat_variable' && isset($path[1])) {
			                        unset($chatVariables[$path[1]]);
			                        $chatVariablesChanged = true;
			                    }
			                } elseif (isset($params['definition']['chat_additional']) && $params['definition']['chat_additional'] != '') {
			                    $paramsAdditions = json_decode($params['definition']['chat_additional'], true);
			                    if (isset($paramsAdditions['identifier'])) {
			                        foreach ($additionalData as $index => $dataAdditional) {
			                            if (isset($dataAdditional['identifier']) && $dataAdditional['identifier'] == $paramsAdditions['identifier']) {
			                                unset($additionalData[$index]);
			                                $additionalDataChanged = true;
			                            }
			                        }
			                    }
			                }
			            }

			            if ($chatVariablesChanged) {
			                $chat->chat_variables_array = $chatVariables;
			                $chat->chat_variables = json_encode($chatVariables);
			                $chatUpdates[] = 'chat_variables';
			            }

			            if ($additionalDataChanged) {
			                $chat->additional_data_array = array_values($additionalData);
			                $chat->additional_data = json_encode(array_values($additionalData));
			                $chatUpdates[] = 'additional_data';
			            }

			            if (!empty($chatUpdates)) {
			                $chat->updateThis(['update' => $chatUpdates]);
			            }
			        }
			    }
			}

			$collected->removeThis();

		} catch (Exception $e) {
			// Do nothing
		}
	}

	erLhcoreClassModule::redirect('form/collected','/'.$form->id);
	exit;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('form/collected').'/'.$form->id . $append;
$pages->items_total = erLhAbstractModelFormCollected::getCount($filter);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhAbstractModelFormCollected::getList(array_merge($filter, array('offset' => $pages->low, 'limit' => $pages->items_per_page, 'sort' => 'id DESC')));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('form/collected').'/'.$form->id;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$tpl->set('form',$form);
$Result['content'] = $tpl->fetch();

$object_trans = $form->getModuleTranslations();
$Result['path'][] = array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/Form','title' => $object_trans['name']);
$Result['path'][] = array('title' => (string)$form);

?>