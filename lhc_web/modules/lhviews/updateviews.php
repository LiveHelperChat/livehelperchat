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

$response = [
    'views' => array_values(erLhAbstractModelSavedSearch::getList(['limit' => false, 'filter' => ['user_id' =>  erLhcoreClassUser::instance()->getUserID()]]))
];

echo json_encode($response);

exit;

?>