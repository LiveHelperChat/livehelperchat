<?php 

header ( 'content-type: application/json; charset=utf-8' );
header ( 'Access-Control-Allow-Origin: *' );
header ( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept' );

if (isset($_POST['data'])) {
	$data = $_POST ['data'];
	$jsonData = json_decode ( $data, true );
	
	try {
		erLhcoreClassChatHelper::mergeVid($jsonData);
	} catch (Exception $e) {
		echo erLhcoreClassChat::safe_json_encode(array('error' => false, 'result' => $e->getMessage()));
	}
}

exit;

?>