<?php

$hash = (string)$Params['user_parameters']['hash'];
$format = (string)$Params['user_parameters_unordered']['format'] == 'xml' ? 'xml' : 'json';

$hashSecret = erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;

try {
	if ( sha1('getlist'.$hashSecret) == $hash ) {

		$filter = array();

		if (is_array($Params['user_parameters_unordered']['status'])){
			foreach ($Params['user_parameters_unordered']['status'] as $status) {
				$filter['filterin']['status'][] = (int)$status;
			}
		}

		$totalChats = erLhcoreClassChat::getCount($filter);

		$pages = new lhPaginator();
		$pages->items_total = $totalChats;
		$pages->setItemsPerPage(is_numeric($Params['user_parameters_unordered']['limit']) ? (int)$Params['user_parameters_unordered']['limit'] : 100);
		$pages->paginate();

		$list = erLhcoreClassChat::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'),$filter));

		if ($format =='json') {
			header('Content-type: application/json');
			echo json_encode(array('list' => array_keys($list)));
			exit;
		} else {
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="utf-8" ?><lhc>';
			foreach (array_keys($list) as $id) {
				echo "<item>{$id}</item>";
			}
			echo '</lhc>';				  
			exit;
		}

	} else {
		throw new Exception('Invalid hash.');
	}

} catch (Exception $e) {

	if ($format =='json') {
		header('Content-type: application/json');
		echo json_encode(array('error' => $e->getMessage()));		
		exit;
	} else {
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="utf-8" ?>',"<lhc><error><![CDATA[".htmlspecialchars($e->getMessage())."]]></error></lhc>";		
		exit;
	}
}

exit;

?>