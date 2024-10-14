<?php

class erLhcoreClassUser{

    static function instance()
    {
        if ( empty( $GLOBALS['LhUserInstance'] ) )
        {
            $GLOBALS['LhUserInstance'] = new erLhcoreClassUser();
        }
        return $GLOBALS['LhUserInstance'];
    }

   function __construct()
   {
       $options = new ezcAuthenticationSessionOptions();
       $options->validity = 3600*24;
       $options->idKey = 'lhc_ezcAuth_id';
       $options->timestampKey = 'lhc_ezcAuth_timestamp';

       $sessionCookieName = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'php_session_cookie_name', false );

       $this->session = new ezcAuthenticationSession($options);
       if (!empty($sessionCookieName) && $sessionCookieName !== false){
           $this->session->setSessionName($sessionCookieName);
       }
       $this->session->start();

       $this->credentials = new ezcAuthenticationPasswordCredentials( $this->session->load(), null );

       if ( !$this->session->isValid( $this->credentials ) )
       {
	       	$logged = false;

	       	if (isset($_COOKIE['lhc_rm_u'])){
	       		$logged = $this->validateRemember($_COOKIE['lhc_rm_u']);
	       	}

	       	if ($logged == false) {
	       		$this->authenticated = false;
	       			       		
	       		if ( isset($_SESSION['lhc_user_id']) )
	       		{
	       			unset($_SESSION['lhc_user_id']);
	       		}

	       		if ( isset($_SESSION['lhc_access_array_'. $this->cache_version]) )
	       		{
	       			unset($_SESSION['lhc_access_array_'. $this->cache_version]);
	       		}

	       		if ( isset($_SESSION['lhc_chat_config']) )
	       		{
	       			unset($_SESSION['lhc_chat_config']);
	       		}
	       	}
	       	
       } else {

          if (isset($_SESSION['lhc_user_id']) && is_numeric($_SESSION['lhc_user_id'])) {
              $this->session->save( $this->session->load() );
              $this->userid = $_SESSION['lhc_user_id'];
              $this->authenticated = true;
              $this->cache_version = $this->getUserData(true)->cache_version;

              // Check that session is valid
              if (self::$oneLoginPerAccount == true || erConfigClassLhConfig::getInstance()->getSetting( 'site', 'one_login_per_account', false ) == true) {              
                  $sesid = $this->getUserData(true)->session_id;             
                  if ($sesid != $_COOKIE[!empty($sessionCookieName) && $sessionCookieName !== false ? $sessionCookieName : 'PHPSESSID'] && $sesid != '') {
                      $this->authenticated = false;
                      $this->logout();
                      $_SESSION['logout_reason'] = 1;
                  } else {
                      $this->authenticated = true;
                  }
              }
          }
       }
   }

   function authenticate($username, $password, $remember = false)
   {
        session_regenerate_id(true);

		$this->session->destroy();

        $sessionCookieName = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'php_session_cookie_name', false );

		$user = erLhcoreClassModelUser::findOne(array(
			'filterlor' => array(
				'username' => array($username),
                'email' => array($username)
			)
		));  

		if ($user === false) {
            return false;
		} else {
            $fieldAuthentificated = $user->email == $username ? 'email' : 'username';
        }

		$cfgSite = erConfigClassLhConfig::getInstance();
		$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );

		if (strlen($user->password) == 40) { // this is old password
		    $passwordVerify = sha1($password.$secretHash.sha1($password));
		    $changePassword = true;
        } else {

    		if (!password_verify($password, $user->password)) {
    			return false;
    		};

    		$changePassword = false;
    		$passwordVerify = $user->password;
	   }

       $this->credentials = new ezcAuthenticationPasswordCredentials( $username, $passwordVerify );

       $database = new ezcAuthenticationDatabaseInfo( ezcDbInstance::get(), 'lh_users', array( $fieldAuthentificated, 'password' ) );
       $this->authentication = new ezcAuthentication( $this->credentials );

       $this->filter = new ezcAuthenticationDatabaseFilter( $database );
       $this->filter->registerFetchData(array('id','username','email','disabled','session_id','cache_version'));

       $this->authentication->addFilter( $this->filter );
       $this->authentication->session = $this->session;

       if ( !$this->authentication->run() ) {
            return false;
            // build an error message based on $status
       } else {
            $data = $this->filter->fetchData();

            if ( $data['disabled'][0] == 0 ) {

                $this->cache_version = $data['cache_version'][0];

            	if ( isset($_SESSION['lhc_access_array_'.$this->cache_version]) ) {
            		unset($_SESSION['lhc_access_array_'.$this->cache_version]);
            	}

                $_SESSION['lhc_user_id'] = $data['id'][0];
                $this->userid = $data['id'][0];

                if ($remember === true) {
                	$this->rememberMe();
                }

                $this->authenticated = true;


                // Limit number per of logins under same user
                if ((self::$oneLoginPerAccount == true || $cfgSite->getSetting( 'site', 'one_login_per_account', false ) == true) && $_COOKIE[!empty($sessionCookieName) && $sessionCookieName !== false ? $sessionCookieName : 'PHPSESSID'] !='') {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare('UPDATE lh_users SET session_id = :session_id WHERE id = :id');
                    $stmt->bindValue(':session_id',session_id(),PDO::PARAM_STR);
                    $stmt->bindValue(':id',$this->userid,PDO::PARAM_INT);
                    $stmt->execute();
                }

                // Change old password to new one
                if ($changePassword === true) {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare('UPDATE lh_users SET password = :password WHERE id = :id');
                    $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                    $stmt->bindValue(':id', $this->userid, PDO::PARAM_INT);
                    $stmt->execute();
                }

                return true;
            }

            return false;
       }
   }

   function getStatus()
   {
       return $this->authentication->getStatus();
   }

   function isLogged()
   {
       return $this->authenticated;
   }

   function getCSFRToken() {

   		if (!isset($_SESSION['lhc_csfr_token'])){
   			$_SESSION['lhc_csfr_token'] = md5(rand(0, 99999999).time().$this->userid);
   		}

   		return $_SESSION['lhc_csfr_token'];
   }

   public static function anonymousGetCSFRToken()
   {
       if (!isset($_SESSION['lhc_csfr_token'])) {
           $_SESSION['lhc_csfr_token'] = md5(rand(0, 99999999).time(). 0);
       }

       return $_SESSION['lhc_csfr_token'];
   }

   public function validateCSFRToken($token) {
   		return $this->getCSFRToken() == $token;
   }

   function setLoggedUser($user_id, $remember = false)
   {
	   	if ($user_id != $this->userid) {

            session_regenerate_id(true);

	   		$this->credentials = new ezcAuthenticationIdCredentials( $user_id );
	   		$this->authentication = new ezcAuthentication( $this->credentials );

	   		$database = new ezcAuthenticationDatabaseInfo( ezcDbInstance::get(), 'lh_users', array( 'id', 'password' ) );
	   		$this->filter = new ezcAuthenticationDatabaseCredentialFilter( $database );
	   		$this->filter->registerFetchData(array('id','username','email','disabled','cache_version'));
	   		$this->authentication->addFilter( $this->filter );

            $sessionCookieName = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'php_session_cookie_name', false );
	   		$this->authentication->session = $this->session;

	   		if ( !$this->authentication->run() ) {
	   			return false;
	   		} else {
	   			$data = $this->filter->fetchData();

   				if ( $data['disabled'][0] == 0 ) {

   					$this->AccessArray = false;
                    $this->cache_version = $data['cache_version'][0];

   					if ( isset($_SESSION['lhc_access_array_'.$this->cache_version]) ) {
   						unset($_SESSION['lhc_access_array_'.$this->cache_version]);
   					}

   					$_SESSION['lhc_user_id'] = $data['id'][0];
   					$this->userid = $data['id'][0];

   					if ($remember == true) {
   					    $this->rememberMe();
                    }

   					$this->authenticated = true;
   					
   					$cfgSite = erConfigClassLhConfig::getInstance();
   					
   					// Limit number per of logins under same user
   					if ((self::$oneLoginPerAccount == true || $cfgSite->getSetting( 'site', 'one_login_per_account', false ) == true) && $_COOKIE[!empty($sessionCookieName) && $sessionCookieName !== false ? $sessionCookieName : 'PHPSESSID'] !='') {
   					    $db = ezcDbInstance::get();
   					    $stmt = $db->prepare('UPDATE lh_users SET session_id = :session_id WHERE id = :id');
   					    $stmt->bindValue(':session_id',session_id(),PDO::PARAM_STR);
   					    $stmt->bindValue(':id',$this->userid,PDO::PARAM_INT);
   					    $stmt->execute();
   					}

   					return true;
   				}

	   			return false;
	   		}
	   	}
   }

   /**
    * 
    * @param string $url url where after logout user should be redirecter
    */
   function logout()
   {
       if (isset($_SESSION['lhc_access_array_'.$this->cache_version])){ unset($_SESSION['lhc_access_array_'.$this->cache_version]); }
       if (isset($_SESSION['lhc_user_id'])){ unset($_SESSION['lhc_user_id']); }
       if (isset($_SESSION['lhc_csfr_token'])){ unset($_SESSION['lhc_csfr_token']); }
       if (isset($_SESSION['lhc_user_timezone'])){ unset($_SESSION['lhc_user_timezone']); }
       if (isset($_SESSION['lhc_chat_config'])){ unset($_SESSION['lhc_chat_config']); }
       
       if ( isset($_COOKIE['lhc_rm_u']) ) {
       		unset($_COOKIE['lhc_rm_u']);
       		setcookie('lhc_rm_u','',time()-31*24*3600,'/');
       };

       if (is_numeric($this->userid)) {       
	       $q = ezcDbInstance::get()->createDeleteQuery();
	
	       // User remember
	       $q->deleteFrom( 'lh_users_remember' )->where( $q->expr->eq( 'user_id', $q->bindValue($this->userid) ) );
	       $stmt = $q->prepare();
	       $stmt->execute();

           erLhcoreClassUserDep::updateLastActivityByUser($this->userid, 0);
       }
       
       $this->session->destroy();
       
       @session_regenerate_id(true);
       @session_destroy();
   }

   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhuser' )
            );
        }
        return self::$persistentSession;
   }

   function getUserData($useCache = false)
   {
      if ($useCache == true && isset($GLOBALS['UserModelCache_'.$this->userid])) return $GLOBALS['UserModelCache_'.$this->userid];

      $GLOBALS['UserModelCache_'.$this->userid] = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', $this->userid );

      return $GLOBALS['UserModelCache_'.$this->userid];
   }

   public function getUserTimeZone() {
   	
   		if (($cacheTimeZone = CSCacheAPC::getMem()->getSession('lhc_user_timezone',true)) !== false){
   			return $cacheTimeZone;
   		}
   		
   		try {
	   		$userData = $this->getUserData(true);   		
	   		CSCacheAPC::getMem()->setSession('lhc_user_timezone',$userData->time_zone,true);
	   		return $userData->time_zone;
   		} catch (Exception $e) {
   			CSCacheAPC::getMem()->setSession('lhc_user_timezone','',true);
   		}
   }
   
   function getUserID()
   {
       return $this->userid;
   }

   function updateLastVisit($lda = 0, $action = 0, $user_id = 0)
   {
       // Because of how user departments table is locked sometimes we have lock deadlines. We need refactor or remove locking for user departments tables.
       try {
             $db = ezcDbInstance::get();
             $db->beginTransaction();

             $user_id = $user_id > 0 ? $user_id : $this->userid;

             if ($lda > 0) {
                 $_SESSION['lhc_online_session_lda'] = $lda;
             }

             if ($action === 2 || $action === 1 || (!isset($_SESSION['lhc_online_session'])) || (isset($_SESSION['lhc_online_session']) && (time() - $_SESSION['lhc_online_session'] > 20))) {

                 erLhcoreClassUserDep::updateLastActivityByUser($user_id, time(), isset($_SESSION['lhc_online_session_lda']) ? (int)$_SESSION['lhc_online_session_lda'] : 0);

                 $userData = $this->getUserData(true);

                 if ($userData->hide_online == 0 || $action === 2 || $action === 1) // Is online or went offline
                 {
                     $id = 0;
                     if ($action !== 1) { // We want previous record only if it's based on activity or visitor went offline
                         $stmt = $db->prepare("SELECT `id` FROM `lh_users_online_session` WHERE `user_id` = :user_id AND `lactivity` > :lactivity_back ORDER BY `id` DESC LIMIT 1");
                         $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
                         $stmt->bindValue(':lactivity_back',time()-40,PDO::PARAM_INT);
                         $stmt->execute();
                         $id = $stmt->fetch(PDO::FETCH_COLUMN);
                     }

                     if (is_numeric($id) && $id > 0) {
                         $stmt = $db->prepare('UPDATE `lh_users_online_session` SET `lactivity` = :lactivity, `duration` = :lactivity_two - `time`, `type` = '.($action > 0 ? (int)$action : '`type`').' WHERE `id` = :id');
                         $stmt->bindValue(':id',$id,PDO::PARAM_INT);
                         $stmt->bindValue(':lactivity_two',time(),PDO::PARAM_INT);
                         $stmt->bindValue(':lactivity',time(),PDO::PARAM_INT);
                         $stmt->execute();
                     } else if ($action !== 2) {
                         $stmt = $db->prepare('INSERT INTO `lh_users_online_session` SET `time` = :time, `type` = :type, `lactivity` = :lactivity, `duration` = 0, `user_id` = :user_id');
                         $stmt->bindValue(':lactivity',time(),PDO::PARAM_INT);
                         $stmt->bindValue(':time',time(),PDO::PARAM_INT);
                         $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
                         $stmt->bindValue(':type',$action,PDO::PARAM_INT);
                         $stmt->execute();
                     }
                 }

                 $_SESSION['lhc_online_session'] = time();
                 $_SESSION['lhc_online_session_lda'] = 0;
             }

             $db->commit();
        } catch (Exception $e) {
           // print_r($e);
           // @todo fix me
        }
   }

   function getUserList()
   {
     $db = ezcDbInstance::get();

     $stmt = $db->prepare('SELECT * FROM lh_users ORDER BY id ASC');
     $stmt->execute();
     $rows = $stmt->fetchAll();

     return $rows;
   }

   public static $permissionsChecks = [];

   function hasAccessTo($module, $functions, $returnLimitation = false)
   {
       static $debugEnabled = null;

       if ($debugEnabled === null) {
           $debugEnabled = erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output');
       }

       if ($debugEnabled == true) {
           self::$permissionsChecks[] = $module . ' | ' . (is_array($functions) ? implode('|',$functions) : $functions);
       }

       $AccessArray = $this->accessArray();

       // Global rights
       if (isset($AccessArray['*']['*']) || isset($AccessArray[$module]['*']))
       {
           if ($returnLimitation === false) {
               return true;
           } elseif (isset($AccessArray[$module]['*']) && !is_bool($AccessArray[$module]['*'])) {
               return $AccessArray[$module]['*'];
           } elseif ($AccessArray['*']['*'] && !is_bool($AccessArray['*']['*'])) {
               return $AccessArray['*']['*'];
           } else {
               return true;
           }
       }

       // Provided rights have to be set
       if (is_array($functions))
       {
           foreach ($functions as $function)
           {
               // Missing one of provided right
               if (!isset($AccessArray[$module][$function])) return false;
           }

       } else {
           if (!isset($AccessArray[$module][$functions])) {
               return false;
           } elseif (isset($AccessArray[$module][$functions]) && $returnLimitation === true && !is_bool($AccessArray[$module][$functions])) {
               return $AccessArray[$module][$functions];
           }
       }

       return true;
   }

   function accessArray()
   {
       if ($this->AccessArray !== false) return $this->AccessArray;

       if (isset($_SESSION['lhc_access_array_'. $this->cache_version]) &&
           $_SESSION['lhc_access_array_'. $this->cache_version] !== false &&
           is_array($_SESSION['lhc_access_array_'. $this->cache_version]) &&
           !empty($_SESSION['lhc_access_array_'. $this->cache_version])) {

           $this->AccessArray = $_SESSION['lhc_access_array_'. $this->cache_version];

           return $this->AccessArray;
       }

       $_SESSION['lhc_access_array_'. $this->cache_version] = $this->AccessArray = $this->generateAccessArray();

       return $this->AccessArray;
   }

   function generateAccessArray()
   {
       $accessArray = erLhcoreClassRole::accessArrayByUserID( $this->userid );

       erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.after_generate_access_array',array('accessArray' => & $accessArray));

       return $accessArray;
   }

   function rememberMe()
   {
	   	$cfgSite = erConfigClassLhConfig::getInstance();
	   	$salt2 = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' );
	   	$salt1 = erLhcoreClassModelForgotPassword::randomPassword(30);
	   	$rusr = new erLhcoreClassModelUserRemember();
	   	$rusr->user_id = $this->userid;
	   	$rusr->mtime = time();
	   	$rusr->saveThis();
	   	$hash = $salt1.':'.$rusr->id.':'.sha1($this->userid.'_'.$rusr->id.$salt2.$salt1.erLhcoreClassIPDetect::getIP().$_SERVER['HTTP_USER_AGENT']);
   		setcookie('lhc_rm_u',$hash,time()+365*24*3600,'/');
   }

   function validateRemember($hashCookie)
   {
	   	$parts = explode(':',$hashCookie);

	   	if (count($parts) == 3){
	   		list($salt1,$id,$hash) = $parts;
	   		$cfgSite = erConfigClassLhConfig::getInstance();
	   		$salt2 = $cfgSite->getSetting( 'site', 'secrethash' );

	   		try {
	   			$ruser = erLhcoreClassModelUserRemember::fetch($id);
	   			if ($hash ==  sha1($ruser->user_id.'_'.$ruser->id.$salt2.$salt1.erLhcoreClassIPDetect::getIP().$_SERVER['HTTP_USER_AGENT'])){
	   				$ruser->mtime = time();
	   				$ruser->updateThis();
	   				$this->setLoggedUser($ruser->user_id);
	   				// Update remember hash
	   				$salt1 = erLhcoreClassModelForgotPassword::randomPassword(30);
	   				$hash = $salt1.':'.$ruser->id.':'.sha1($this->userid.'_'.$ruser->id.$salt2.$salt1.erLhcoreClassIPDetect::getIP().$_SERVER['HTTP_USER_AGENT']);
	   				setcookie('lhc_rm_u',$hash,time()+365*24*3600,'/');
	   				return true;
	   			}
	   		} catch (Exception $e){
	   			return false;
	   		}
	   	} else {
	   		if ( isset($_COOKIE['lhc_rm_u']) ) {
	   			unset($_COOKIE['lhc_rm_u']);
	   			setcookie('lhc_rm_u','',time()-31*24*3600,'/');
	   		};
	   	}

	   	return false;
   }

   private static $persistentSession;
   private $userid;
   private $AccessArray = false;

   // This variable will be set to true based on online hosting record
   public static $oneLoginPerAccount = false;
   
   // Authentification things
   public $authentication;
   public $session;
   public $credentials;
   public $authenticated;
   public $status;
   public $filter;
   public $cache_version = 0;

}


?>