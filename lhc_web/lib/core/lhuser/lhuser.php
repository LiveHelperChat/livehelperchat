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

       $this->session = new ezcAuthenticationSession($options);
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

	       		if ( isset($_SESSION['user_id']) )
	       		{
	       			unset($_SESSION['user_id']);
	       			unset($_SESSION['access_array']);
	       			unset($_SESSION['access_timestamp']);
	       		}
	       	}

       } else {

          $this->session->save( $this->session->load() );
          $this->userid = $_SESSION['lhc_user_id'];
          $this->authenticated = true;
       }
   }

   function authenticate($username,$password,$remember = false)
   {
       $this->session->destroy();

       $cfgSite = erConfigClassLhConfig::getInstance();
	   $secretHash = $cfgSite->getSetting( 'site', 'secrethash' );

       $this->credentials = new ezcAuthenticationPasswordCredentials( $username, sha1($password.$secretHash.sha1($password)) );

       $database = new ezcAuthenticationDatabaseInfo( ezcDbInstance::get(), 'lh_users', array( 'username', 'password' ) );
       $this->authentication = new ezcAuthentication( $this->credentials );

       $this->filter = new ezcAuthenticationDatabaseFilter( $database );
       $this->filter->registerFetchData(array('id','username','email','disabled'));

       $this->authentication->addFilter( $this->filter );
       $this->authentication->session = $this->session;

       if ( !$this->authentication->run() ) {
            return false;
            // build an error message based on $status
       } else {
            $data = $this->filter->fetchData();

            if ( $data['disabled'][0] == 0 ) {

            	if ( isset($_SESSION['lhc_access_array']) ) {
            		unset($_SESSION['lhc_access_array']);
            	}

            	if ( isset($_SESSION['lhc_access_timestamp']) ) {
            		unset($_SESSION['lhc_access_timestamp']);
            	}

                $_SESSION['lhc_user_id'] = $data['id'][0];
                $this->userid = $data['id'][0];

                if ($remember === true) {
                	$this->rememberMe();
                }

                $this->authenticated = true;
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

   function setLoggedUser($user_id)
   {
	   	if ($user_id != $this->userid) {

	   		$this->credentials = new ezcAuthenticationIdCredentials( $user_id );
	   		$this->authentication = new ezcAuthentication( $this->credentials );

	   		$database = new ezcAuthenticationDatabaseInfo( ezcDbInstance::get(), 'lh_users', array( 'id', 'password' ) );
	   		$this->filter = new ezcAuthenticationDatabaseCredentialFilter( $database );
	   		$this->filter->registerFetchData(array('id','username','email','disabled'));
	   		$this->authentication->addFilter( $this->filter );

	   		$this->authentication->session = $this->session;

	   		if ( !$this->authentication->run() ) {
	   			return false;
	   		} else {
	   			$data = $this->filter->fetchData();

   				if ( $data['disabled'][0] == 0 ) {

   					$this->AccessArray = false;

   					if ( isset($_SESSION['lhc_access_array']) ) {
   						unset($_SESSION['lhc_access_array']);
   					}

   					if ( isset($_SESSION['lhc_access_timestamp']) ) {
   						unset($_SESSION['lhc_access_timestamp']);
   					}

   					$_SESSION['lhc_user_id'] = $data['id'][0];
   					$this->userid = $data['id'][0];

   					$this->authenticated = true;
   					return true;
   				}

	   			return false;
	   		}
	   	}
   }

   function logout()
   {
       if (isset($_SESSION['lhc_access_array'])){ unset($_SESSION['lhc_access_array']); }
       if (isset($_SESSION['lhc_access_timestamp'])){ unset($_SESSION['lhc_access_timestamp']); }
       if (isset($_SESSION['lhc_user_id'])){ unset($_SESSION['lhc_user_id']); }

       if ( isset($_COOKIE['lhc_rm_u']) ) {
       		unset($_COOKIE['lhc_rm_u']);
       		setcookie('lhc_rm_u','',time()-31*24*3600,'/');
       };

       $this->session->destroy();
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

   function getUserID()
   {
       return $this->userid;
   }

   function updateLastVisit()
   {
       $db = ezcDbInstance::get();
       $db->query('UPDATE lh_userdep SET last_activity = '.time().' WHERE user_id = '.$this->userid);
   }

   function getUserList()
   {
     $db = ezcDbInstance::get();

     $stmt = $db->prepare('SELECT * FROM lh_users ORDER BY id ASC');
     $stmt->execute();
     $rows = $stmt->fetchAll();

     return $rows;
   }

   function hasAccessTo($module, $functions)
   {
       $AccessArray = $this->accessArray();

       // Global rights
       if (isset($AccessArray['*']['*']) || isset($AccessArray[$module]['*']))
       {
           return true;
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
           if (!isset($AccessArray[$module][$functions])) return false;
       }

       return true;
   }

   function accessArray()
   {
       if ($this->AccessArray !== false) return $this->AccessArray;

       if (isset($_SESSION['lhc_access_array'])) {

           $this->AccessArray = $_SESSION['lhc_access_array'];
           $this->AccessTimestamp =  $_SESSION['lhc_access_timestamp'];

           $cacheObj = CSCacheAPC::getMem();

           if (($AccessTimestamp = $cacheObj->restore('cachetimestamp_accessfile_version_'.$cacheObj->getCacheVersion('site_version'))) === false)
           {
               $cfg = erConfigClassLhCacheConfig::getInstance();
               $AccessTimestamp = $cfg->getSetting( 'cachetimestamps', 'accessfile' );
               $cacheObj->store('cachetimestamp_accessfile_version_'.$cacheObj->getCacheVersion('site_version'),$AccessTimestamp);
           }

           if ( $this->AccessTimestamp === $AccessTimestamp)
           {
               return $this->AccessArray;
           }
       }

       if ($this->cacheCreated == false) {
           $this->cacheCreated = true;
           ezcCacheManager::createCache( 'userinfo', 'cache/userinfo', 'ezcCacheStorageFileArray', array('ttl'   => 60*60*24*1 ) );
       }

       $cache = ezcCacheManager::getCache( 'userinfo' );

       $id = $this->userid;

       $cfg = erConfigClassLhCacheConfig::getInstance();

       $AccessTimestamp = $cfg->getSetting( 'cachetimestamps', 'accessfile' );
       $CheckExpire = false;

       if ( ( $data = $cache->restore( $id ) ) === false || $AccessTimestamp < time() )
       {
            $this->AccessArray = $this->generateAccessArray();

            $data['access_array'] = $this->AccessArray;
            $data['access_timestamp'] = $AccessTimestamp;
            $this->AccessTimestamp = $AccessTimestamp;

            if ($AccessTimestamp < time() )
            {
                $AccessTimestamp = time() + 60*60*24*1;
                $cfg->setSetting( 'cachetimestamps', 'accessfile', $AccessTimestamp );
                $cfg->save();
                $data['access_timestamp'] = $AccessTimestamp;
                $this->AccessTimestamp = $AccessTimestamp;
            }

            // Do not store empty access_array
            if ( !empty($data['access_array']) ) {
                $cache->store( $id, $data );
            }

            $_SESSION['lhc_access_array'] = $this->AccessArray;


       } else {
           $CheckExpire = true;
           $this->AccessArray = $data['access_array'];
           $this->AccessTimestamp = $data['access_timestamp'];
       }

       if ( $CheckExpire === true && $data['access_timestamp'] != $AccessTimestamp)
       {
           $this->AccessArray = $this->generateAccessArray();
           $this->AccessTimestamp = $AccessTimestamp;
           $data['access_timestamp'] = $AccessTimestamp;
           $data['access_array'] = $this->AccessArray;

           if ( !empty($data['access_array']) ) {
               $cache->store( $id, $data );
           }
       }

       $_SESSION['lhc_access_array'] = $this->AccessArray;
       $_SESSION['lhc_access_timestamp'] = $this->AccessTimestamp;

       return $this->AccessArray;
   }

   function generateAccessArray()
   {
       $accessArray = erLhcoreClassRole::accessArrayByUserID( $this->userid );

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
	   	$hash = $salt1.':'.$rusr->id.':'.sha1($this->userid.'_'.$rusr->id.$salt2.$salt1.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
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
	   			if ($hash ==  sha1($ruser->user_id.'_'.$ruser->id.$salt2.$salt1.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])){
	   				$ruser->mtime = time();
	   				$ruser->updateThis();
	   				$this->setLoggedUser($ruser->user_id);
	   				// Update remember hash
	   				$salt1 = erLhcoreClassModelForgotPassword::randomPassword(30);
	   				$hash = $salt1.':'.$ruser->id.':'.sha1($this->userid.'_'.$ruser->id.$salt2.$salt1.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
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
   private $AccessTimestamp = false;
   private $cacheCreated = false;


   // Authentification things
   public $authentication;
   public $session;
   public $credentials;
   public $authenticated;
   public $status;
   public $filter;

}


?>