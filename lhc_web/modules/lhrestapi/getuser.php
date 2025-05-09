<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    // init data
    $user_id        = isset($_GET['user_id'])? intval($_GET['user_id']) : (isset($_POST['user_id']) ? intval($_POST['user_id']) : 0);
    $username    = isset($_GET['username'])? trim($_GET['username']) : (isset($_POST['username']) ? trim($_POST['username']) : '');
    $email          = isset($_GET['email'])? trim($_GET['email']) : (isset($_POST['email']) ? trim($_POST['email']) : '');
    $password    = isset($_GET['password'])? trim($_GET['password']): (isset($_POST['password']) ? trim($_POST['password']) : '');
    
    // init param, check what is supplied
    $param          = ($username != '')? array('username' => $username) : array('email' => '00'); // dummy email value to ensure 0 res
    $param          = ($email != '')? array('email' => $email) : $param;
     
    // init user
    $user = ($user_id > 0)? erLhcoreClassModelUser::fetch($user_id) : erLhcoreClassModelUser::findOne(array('filter' => $param));

    if (!($user instanceof erLhcoreClassModelUser) && empty($email) && !empty($username) && str_contains($username, '@')) {
        $param = array('email' => $username);
        $user = erLhcoreClassModelUser::findOne(array('filter' => $param));
    }

    // check we have data
    if (! ($user instanceof erLhcoreClassModelUser)) 
    {
        throw new Exception('User could not be found!');
    }

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'userlist') && $user->id != erLhcoreClassRestAPIHandler::getUserId()) {
        throw new Exception('You do not have permission to list a users. `lhuser`, `userlist` is required or you have to be an owner of user you want to fetch.');
    }

    // check if password is given, if so, validate password
    if($password != '')
    {
        // check password encryption type
        if (strlen($user->password) == 40)
        {
            // get password hash
            $cfgSite = erConfigClassLhConfig::getInstance();
            $secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
            
            $pass_hash   = sha1($password.$secretHash.sha1($password));
            
            $verified       = ($user->password == $pass_hash)? 1 : 0;
        }
        else
        {
            $verified = (password_verify($password, $user->password))? 1 : 0;
        }
        
        // set new property to user object
        $user->pass_verified = $verified;
    } // end of if($password != '')
    
    // loose password
    unset($user->password);
    
    erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => $user));
    
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();
