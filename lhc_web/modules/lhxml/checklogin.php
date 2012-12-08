<?php

// Debug
//erLhcoreClassLog::write(print_r($_POST,true));

$currentUser = erLhcoreClassUser::instance();

if ($currentUser->authenticate($_POST['username'],$_POST['password']))
{     
        echo json_encode(
            array('result' => true)
        );
          
} else {
    echo json_encode(
            array('result' => false)
        );    
}
  

exit;
?>