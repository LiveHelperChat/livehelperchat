<?php

header ( 'content-type: application/json; charset=utf-8' );

$q = ezcDbInstance::get()->createUpdateQuery();
$q->update( 'lh_abstract_saved_search' )
      ->set( 'requested_at', time() )
      ->where(
          $q->expr->eq('user_id', $q->bindValue((int)erLhcoreClassUser::instance()->getUserID()))
      );
$stmt = $q->prepare();
$stmt->execute();

$views = erLhAbstractModelSavedSearch::getList(['limit' => false, 'filter' => ['user_id' =>  erLhcoreClassUser::instance()->getUserID()]]);

erLhcoreClassChat::prefillGetAttributes($views, array(
    'id',
    'name',
    'scope',
    'passive',
    'total_records',
    'updated_ago'), array(), array('remove_all' => true));

$response = [
    'views' => array_values($views)
];

echo json_encode($response);

exit;

?>