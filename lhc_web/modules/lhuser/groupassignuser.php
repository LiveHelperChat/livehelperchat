<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/groupassignuser.tpl.php');
$tpl->set('group_id',(int)$Params['user_parameters']['group_id']);

if (isset($_POST['AssignUsers']) && isset($_POST['UserID']) && count($_POST['UserID']) > 0)
{
   foreach ($_POST['UserID'] as $UserID)
   {                
        $GroupUser = new erLhcoreClassModelGroupUser();        
        $GroupUser->group_id = (int)$Params['user_parameters']['group_id'];
        $GroupUser->user_id = $UserID;
        erLhcoreClassUser::getSession()->save($GroupUser);
   }
    
   $tpl->set('assigned',true);
    
} else {
    $session = erLhcoreClassUser::getSession();
    $q = $session->database->createSelectQuery();  
    
    $q2 = $session->database->createSelectQuery();  
    $q2->select( "user_id" )->from( "lh_groupuser" );
    $q2->where($q2->expr->eq( 'group_id', (int)$Params['user_parameters']['group_id'] ));
    
    $q->select( "COUNT(lh_users.id)" )->from( "lh_users" );
    $q->where(
        'lh_users.id NOT IN ('. (string)$q2.')'
    );      
    
    $stmt = $q->prepare();       
    $stmt->execute();  
    $result = $stmt->fetchColumn();
          
    $pages = new lhPaginator();
    $pages->items_total = $result;
    $pages->setItemsPerPage(10);
    $pages->serverURL = erLhcoreClassDesign::baseurl('user/groupassignuser').'/'.(int)$Params['user_parameters']['group_id'];
    $pages->paginate();
    
    $tpl->set('pages',$pages);
    
    if ($pages->items_total > 0){
        
        $session = erLhcoreClassUser::getSession();
        $q = $session->createFindQuery( 'erLhcoreClassModelUser' ); 
        
        $q2 = $session->database->createSelectQuery();  
        $q2->select( "user_id" )->from( "lh_groupuser" );
        $q2->where($q2->expr->eq( 'group_id', (int)$Params['user_parameters']['group_id'] )); 
        $q->where(
            'lh_users.id NOT IN ('. (string)$q2.')'        
        );    
        $q->limit($pages->items_per_page,$pages->low);
        $q->orderBy('id DESC');
                  
        $users = $session->find( $q );
        $tpl->set('users',$users);
    } else {
        $tpl->set('users',array());
    }
}
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';