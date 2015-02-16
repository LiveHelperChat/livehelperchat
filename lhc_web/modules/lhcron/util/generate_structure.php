<?php 

// php cron.php -s site_admin -c cron/util/generate_structure > doc/update_db/structure.json

$structureTables = array(
	'lh_abstract_auto_responder',
	'lh_abstract_browse_offer_invitation',
	'lh_abstract_email_template',
	'lh_abstract_form',
	'lh_abstract_form_collected',
	'lh_abstract_proactive_chat_invitation',
	'lh_abstract_widget_theme',
	'lh_canned_msg',
	'lh_chat',
	'lh_chat_accept',
	'lh_chat_archive_range',
	'lh_chat_blocked_user',
	'lh_chat_config',
	'lh_chat_file',
	'lh_chat_online_user',
	'lh_chat_online_user_footprint',
	'lh_chatbox',
	'lh_departament',
	'lh_faq',
	'lh_forgotpasswordhash',
	'lh_group',
	'lh_grouprole',
	'lh_groupuser',
	'lh_msg',
	'lh_question',
	'lh_question_answer',
	'lh_question_option',
	'lh_question_option_answer',
	'lh_role',
	'lh_rolefunction',
	'lh_transfer',
	'lh_userdep',
	'lh_users',
	'lh_users_remember',
	'lh_users_setting',
	'lh_users_setting_option',
);

$dataTables = array (
	'lh_users_setting_option' => 'identifier',
	'lh_chat_config' => 'identifier',
	'lh_abstract_email_template' => 'id',
);


// Array which holds our version definition
$structuresTablesData = array();

$db = ezcDbInstance::get();

foreach ($structureTables as $table) {
	$sql = 'SHOW COLUMNS FROM '.$table;			
	$stmt = $db->prepare($sql);
	$stmt->execute();			
	$columnsData = $stmt->fetchAll(PDO::FETCH_ASSOC);	
	$structuresTablesData['tables'][$table] = $columnsData;	
}

foreach ($dataTables as $table => $identifier) {
	$sql = 'SELECT * FROM '.$table;			
	$stmt = $db->prepare($sql);
	$stmt->execute();			
	$recordsData = $stmt->fetchAll(PDO::FETCH_ASSOC);	
	$structuresTablesData['tables_data'][$table] = $recordsData;	
	$structuresTablesData['tables_data_identifier'][$table] = $identifier;
}

foreach ($structureTables as $table) {
	$sql = 'SHOW CREATE TABLE `'.$table.'`';			
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$recordsData = $stmt->fetch(PDO::FETCH_ASSOC);	
	$structuresTablesData['tables_create'][$table] = $recordsData['create table'];
}

echo json_encode($structuresTablesData,JSON_PRETTY_PRINT);

?>