<?php

class erLhcoreClassModule{

    static function runModule()
    {
        if (isset(self::$currentModule[self::$currentView]))
        {
            $Params = array();
            $Params['module'] = self::$currentModule[self::$currentView];
            $Params['module']['name'] = self::$currentModule;
            $Params['module']['view'] = self::$currentView;

            $urlCfgDefault = ezcUrlConfiguration::getInstance();
            $url = erLhcoreClassURL::getInstance();
            
            self::$currentModule[self::$currentView]['uparams'][] = 'page';

            foreach (self::$currentModule[self::$currentView]['params'] as $userParameter)
            {
               $urlCfgDefault->addOrderedParameter( $userParameter );
            }

            foreach (self::$currentModule[self::$currentView]['uparams'] as $userParameter)
            {
               $urlCfgDefault->addUnorderedParameter( $userParameter,isset(self::$currentModule[self::$currentView]['multiple_arguments']) && in_array($userParameter,self::$currentModule[self::$currentView]['multiple_arguments']) ? ezcUrlConfiguration::MULTIPLE_ARGUMENTS : null );
            }

            $url->applyConfiguration( $urlCfgDefault );

            foreach (array_merge(self::$currentModule[self::$currentView]['uparams'],self::$currentModule[self::$currentView]['params']) as $userParameter)
            {
                $Params[in_array($userParameter,self::$currentModule[self::$currentView]['params']) ? 'user_parameters' : 'user_parameters_unordered'][$userParameter] = $url->getParam($userParameter);
            }

            // Function set, check permission
            if (isset($Params['module']['functions']))
            {
            	// Just to start session
            	$currentUser = erLhcoreClassUser::instance();
                header('Cache-Control: nocache, no-store, max-age=0, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: Sun, 02 Jan 1990 00:00:00 GMT');

                if (!$currentUser->hasAccessTo('lh'.self::$currentModuleName,$Params['module']['functions']))
                {
                	if ($currentUser->isLogged()) {
	                 	include_once('modules/lhkernel/nopermission.php');
	                 	$Result['pagelayout'] = 'login';
	                   	return $Result;
                   	} else {
                   	    if (isset($Params['module']['ajax']) && $Params['module']['ajax'] == true){
                            header('X-Robots-Tag: noindex');
                   	        echo json_encode(array('error' => true, 'error_url' => erLhcoreClassDesign::baseurl('user/login') . '/(noaccess)/true'));
                   	        exit;
                   	    } else {
                               $r = '/(r)/'.rawurlencode(base64_encode(substr($url->buildUrl(), strrpos($url->buildUrl(),self::$currentModuleName.'/'.self::$currentView))));
                               self::redirect('user/login',$r);
                               exit;
                   	    }
                   	}
                }
            }

            if (isset($Params['module']['limitations']))
            {
            	// Just to start session
            	$currentUser = erLhcoreClassUser::instance();

                $access = call_user_func($Params['module']['limitations']['self']['method'],$Params['user_parameters'][$Params['module']['limitations']['self']['param']],$currentUser->hasAccessTo('lh'.self::$currentModuleName,$Params['module']['limitations']['global']));

                if ($access == false) {

                	if ($currentUser->isLogged()) {
	                	include_once('modules/lhkernel/nopermissionobject.php');
	                	$Result['pagelayout'] = 'login';
	                   	return $Result;
                	} else {
                		self::redirect('user/login');
                		exit;
                	}


                } else {
                	$Params['user_object'] = $access;
                }
            }
          
            try {

                // Send X-Frame-Options if URL is private
                // Or it's site_admin based one
                if (isset($currentUser) || erLhcoreClassSystem::instance()->SiteAccess == 'site_admin') {
                    header('X-Frame-Options: SAMEORIGIN');
                }

            	if (isset($currentUser) && $currentUser->isLogged() && ($timeZone = $currentUser->getUserTimeZone()) != '') {    
            		self::$defaultTimeZone = $timeZone;
            		date_default_timezone_set(self::$defaultTimeZone);            		
            	} elseif (self::$defaultTimeZone != '') {            	
            		date_default_timezone_set(self::$defaultTimeZone);
            	}

                $fileLocation = self::getModuleFile();
                if (file_exists($fileLocation)) {
                    $startTime = microtime();

                    $includeStatus = include($fileLocation);

                    self::logSlowRequest($startTime, microtime(),(isset($currentUser) && $currentUser->isLogged() ? $currentUser->getUserID() : 0));
                } else {
                    $includeStatus = false;
                }


            	            	
            	// Inclusion failed
            	if ($includeStatus === false) {
            		$CacheManager = erConfigClassLhCacheConfig::getInstance();
            		$CacheManager->expireCache();

            		if (self::$debugEnabled == true) {
	            		// Just try reinclude
	            		include(self::getModuleFile(true));
            		} else {
            			// Just try reinclude
            			@include(self::getModuleFile(true));
            		}
            		
            		if (!isset($Result)) {
            			self::redirect( self::$currentModuleName . '/' . self::$currentView);
            			exit;
            		}
            	}

            } catch (Exception $e) {
            	$CacheManager = erConfigClassLhCacheConfig::getInstance();
            	$CacheManager->expireCache();

				if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
					echo "<pre>";
					print_r($e);
					echo "</pre>";
					exit;
				}

				if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'installed' ) == false) {
					header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
					exit;
				}

                error_log($e);

            	header('HTTP/1.1 503 Service Temporarily Unavailable');
            	header('Status: 503 Service Temporarily Unavailable');
            	header('Retry-After: 300');

            	if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'],'application/json') !== false){
                    echo json_encode(array('error' => true, 'message' => $e->getMessage()));
                } else {
                    include_once('design/defaulttheme/tpl/lhkernel/fatal_error.tpl.php');
                    erLhcoreClassLog::write(print_r($e,true));
                    // Try to store to DB directly error
                    self::logException($e);
                }

                exit;
            }

            if (isset($Params['module']['pagelayout']) && !isset($Result['pagelayout'])) {
                $Result['pagelayout'] = $Params['module']['pagelayout'];
            }

            return $Result;
        } else {

            // Default module view
            if (($viewDefault = self::getModuleDefaultView(self::$currentModuleName)) !== false) {
                self::redirect(self::$currentModuleName . '/' . $viewDefault);
                exit;
            }
            // No sutch module etc, redirect to frontpage
            self::redirect();
            exit;
        }
    }

    public static function getDifference($start_time, $end_time) {
        $start = explode(' ', $start_time);
        $end = explode(' ', $end_time);
        return round($end[0] + $end[1] - $start[0] - $start[1],3);
    }

    public static function logSlowRequest( $start_time, $end_time, $object_id, $message = [])
    {
        if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'log_slow_request', false ) !== true) {
            return;
        }

        $time = self::getDifference($start_time, $end_time);

        if ($time > 2) {

            $message['post'] = $_POST;
            $message['get'] = $_GET;
            $message['payload'] = file_get_contents('php://input');
            $message['taken_Time'] = $time;

            self::logException(new Exception(json_encode($message, JSON_PRETTY_PRINT)), 'slow_request', $object_id);
        }
    }

    public static function reRun($url) {
              
        $sysConfiguration = erLhcoreClassSystem::instance()->RequestURI = $url;
        
        erLhcoreClassURL::resetInstance();
        
        return self::moduleInit(array('ignore_extensions' => false));
    }

    public static function defaultExceptionHandler($e)
    {

        if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            exit;
        }

        error_log($e);

        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 300');


        include_once('design/defaulttheme/tpl/lhkernel/fatal_error.tpl.php');

        if (file_exists('cache/default.log') && (filesize('cache/default.log')/1000) > 200){
            file_put_contents('cache/default.log', date('M j H:i:s') . ' [Warning] [default] [default] '. print_r($e,true));
        } else {
            file_put_contents('cache/default.log',date('M j H:i:s') . ' [Warning] [default] [default] '. print_r($e,true), FILE_APPEND);
        }

        self::logException($e);
    }

    public static function logException($e, $category = 'web_exception', $object_id = 0) {
        // Try to store to DB directly error
        try {
            $cfg = erConfigClassLhConfig::getInstance();
            $conn = new PDO("mysql:host=".$cfg->getSetting( 'db', 'host' ).";dbname=".$cfg->getSetting( 'db', 'database' ), $cfg->getSetting( 'db', 'user' ), $cfg->getSetting( 'db', 'password' ));
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("INSERT INTO `lh_audits` (`category`, `source`, `line`, `file`, `object_id`, `message`, `severity`, `time`) VALUES (:category, 'lhc',:line,:file, :object_id, :message,:severity,:time)");
            $stmt->bindValue(':category', $category);
            $stmt->bindValue(':object_id', $object_id);
            $stmt->bindValue(':line',__LINE__);
            $stmt->bindValue(':file',__FILE__);
            $stmt->bindValue(':severity',ezcLog::SUCCESS_AUDIT);
            $stmt->bindValue(':time',date('Y-m-d H:i:s'));
            $stmt->bindValue(':message',json_encode([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'server' => $_SERVER,
                'trace' => $e->getTrace()
            ],JSON_PRETTY_PRINT));
            $stmt->execute();
        } catch(PDOException $e) {

        }
    }

    public static function defaultWarningHandler($errno, $errstr, $errfile, $errline) {

        if ($errno == E_USER_ERROR || $errno == E_COMPILE_ERROR || $errno == E_PARSE || $errno == E_ERROR || $errno ==  E_RECOVERABLE_ERROR) {
            $msg = 'Unexpected error, the message was : ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;
            error_log('Unexpected error, the message was : ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
            erLhcoreClassLog::write($msg);
            include_once('design/defaulttheme/tpl/lhkernel/fatal_error.tpl.php');

            $trace = debug_backtrace();

            @erLhcoreClassLog::write(
                json_encode([
                    'msg' => $msg,
                    'trace' => $trace
                ]),
                ezcLog::SUCCESS_AUDIT,
                array(
                    'source' => 'lhc',
                    'category' => 'web_fatal',
                    'line' => __LINE__,
                    'file' => __FILE__,
                    'object_id' => 0
                )
            );

            exit(1);
            return true;
        }

        if ($errno == E_WARNING && strpos($errstr,'include(): Failed opening \'cache/')) {
            erLhcoreClassLog::write('Unexpected warning, the message was : ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
        }

        if ($errno == E_NOTICE || $errno == E_STRICT) {
            // erLhcoreClassLog::write('Unexpected notice, the message was : ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
        }
        
        return false;
    }

    public static function attatchExtensionListeners(){
    	$cfg = erConfigClassLhConfig::getInstance();
    	$extensions = $cfg->getOverrideValue('site','extensions');
    	
    	// Is it extension module
    	foreach ($extensions as $extension)
    	{
    		if (file_exists('extension/'.$extension.'/bootstrap/bootstrap.php')){
    			include('extension/'.$extension.'/bootstrap/bootstrap.php');
    			$className = 'erLhcoreClassExtension'.ucfirst($extension);
    			$class = new $className();
    			$class->run();
    			self::$extensionsBootstraps[$className] = $class;
    		}
    	}    	
    }
    
    public static function getExtensionInstance($className)
    {
        if (isset(self::$extensionsBootstraps[$className])) {
            return self::$extensionsBootstraps[$className];
        }
        
        return false;
    }
    
    public static function getModuleDefaultView($module)
    {
        $cfg = erConfigClassLhConfig::getInstance();
        $extensions = $cfg->getOverrideValue('site','extensions');

        // Is it core module
        if (file_exists('modules/lh'.$module.'/module.php')) {
            include('modules/lh'.$module.'/module.php');
        }

        // Is it extension module
        foreach ($extensions as $extension)
        {
            if (file_exists('extension/'.$extension.'/modules/lh'.$module.'/module.php')){
                include('extension/'.$extension.'/modules/lh'.$module.'/module.php');
             }
        }

        if (isset($Module['default_function'])) return $Module['default_function'];

        return false;
    }

    public static function getModuleFile($disableCacheManually = false) {

        $cfg = erConfigClassLhConfig::getInstance();
        $cacheEnabled = $cfg->getSetting( 'site', 'modulecompile' );

        if ($cacheEnabled === false || $disableCacheManually === true) {
            return self::$currentModule[self::$currentView]['script_path'];
        } else {

            $instance = erLhcoreClassSystem::instance();
            $cacheKey = md5(self::$currentModuleName.'_'.self::$currentView.'_'.$instance->WWWDirLang.'_'.$instance->Language);

            if ( ($cacheModules = self::$cacheInstance->restore('moduleCache_'.self::$currentModuleName.'_version_'.self::$cacheVersionSite)) !== false && key_exists($cacheKey,$cacheModules))
            {
            	return $cacheModules[$cacheKey];
            }

            $cacheWriter = new erLhcoreClassCacheStorage('cache/cacheconfig/');
            if (($cacheModules = $cacheWriter->restore('moduleCache_'.self::$currentModuleName)) == false)
            {
            	$cacheWriter->store('moduleCache_'.self::$currentModuleName,array());
            	$cacheModules = array();
            }

            if (key_exists($cacheKey,$cacheModules))
            {
                    self::$cacheInstance->store('moduleCache_'.self::$currentModuleName.'_version_'.self::$cacheVersionSite,$cacheModules);
            		return $cacheModules[$cacheKey];
            }

            $file = self::$currentModule[self::$currentView]['script_path'];
            $contentFile = php_strip_whitespace($file);

            // We want to compile as it was live module run without escaping translations
            if (strpos($contentFile,'erTranslationClassLhTranslation::$htmlEscape = false') !== false) {
                erTranslationClassLhTranslation::$htmlEscape = false;
            }

            $Matches = array();
			preg_match_all('/erTranslationClassLhTranslation::getInstance\(\)->getTranslation\(\'(.*?)\',(.*?)\'(.*?)\'\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $TranslateContent)
			{
				$contentFile = str_replace($Matches[0][$key],'\'' .str_replace("'","\'",erTranslationClassLhTranslation::getInstance()->getTranslation($TranslateContent,$Matches[3][$key])) .'\'',$contentFile);
			}
			
			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::baseurl\((.*?)\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::baseurl(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

			// Compile additional JS
			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::designJS\(\'(.*?)\'\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{			  
			    $contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::designJS(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

            $Matches = array();
			preg_match_all('/erLhcoreClassDesign::design\(\'(.*?)\'\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::design(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

            $Matches = array();
			preg_match_all('/erLhcoreClassDesign::designCSS\(\'(.*?)\'\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::designCSS(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::baseurldirect\(\'(.*?)\'\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::baseurldirect(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

			$contentFile = str_replace('erLhcoreClassSystem::instance()->SiteAccess','\''.erLhcoreClassSystem::instance()->SiteAccess.'\'',$contentFile);

			$Matches = array();
			preg_match_all('/erConfigClassLhConfig::getInstance\(\)->getSetting\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?),(\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $valueConfig = erConfigClassLhConfig::getInstance()->getSetting($Matches[2][$key],$Matches[5][$key]);
			    $valueReplace = '';

			    if (is_bool($valueConfig)){
			        $valueReplace = $valueConfig == false ? 'false' : 'true';
			    } elseif (is_integer($valueConfig)) {
			        $valueReplace = $valueConfig;
			    } elseif (is_array($valueConfig)) {
			        $valueReplace = var_export($valueConfig,true);
			    } else {
			        $valueReplace = '\''.$valueConfig.'\'';
			    }

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			if (self::$cacheDbVariables === true) {
			
			    $fetchMethods = array(
			        'fetch',
			        'fetchCache'
			    );
			    
			    foreach ($fetchMethods as $fetchMethod) {
    				// Compile config settings
    	            $Matches = array();
    	            preg_match_all('/erLhcoreClassModelChatConfig::'.$fetchMethod.'\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->current_value/i',$contentFile,$Matches);
    	            foreach ($Matches[1] as $key => $UrlAddress)
    	            {
    	                $valueConfig = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->current_value;
    	                $valueReplace = '\''.str_replace("'","\'",$valueConfig).'\'';
    	                $contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
    	            }
    		            
    	            // Compile config settings in php scripts
    	            $Matches = array();
    	            preg_match_all('/erLhcoreClassModelChatConfig::'.$fetchMethod.'\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->data_value/i',$contentFile,$Matches);
    	            foreach ($Matches[1] as $key => $UrlAddress)
    	            {
    	            	$valueConfig = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->data_value;
    	            	$valueReplace = var_export($valueConfig,true);
    	            	$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
    	            }
    	            	            
    	            // Compile config settings array
    	            $Matches = array();
    	            preg_match_all('/erLhcoreClassModelChatConfig::'.$fetchMethod.'\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->data\[\'([a-zA-Z0-9-\.-\/\_]+)\'\]/i',$contentFile,$Matches);
    	            foreach ($Matches[1] as $key => $UrlAddress)
    	            {
    	                $valueHolder = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->data;
    	            	$valueConfig = isset($valueHolder[$Matches[4][$key]]) ? $valueHolder[$Matches[4][$key]] : '';
    	            	$valueReplace = '';
    	            	$valueReplace = '\''.str_replace("'","\'",$valueConfig).'\'';
    	            	$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
    	            }
			    }
			}

            $fileCompiled = 'cache/compiledtemplates/'.md5($file.$instance->WWWDirLang.'_'.$instance->Language).'.php';

            // Atomoc template compilation to avoid concurent request compiling and writing to the same file
            $fileTemp = 'cache/cacheconfig/'.md5(time().microtime().rand(0, 1000).$file.$instance->WWWDirLang.'_'.$instance->Language).'.php';
            file_put_contents($fileTemp,$contentFile);

            // Atomic file write
            rename($fileTemp,$fileCompiled);

			$cacheModules[$cacheKey] = $fileCompiled;


			$cacheWriter->store('moduleCache_'.self::$currentModuleName,$cacheModules);
			self::$cacheInstance->store('moduleCache_'.self::$currentModuleName.'_version_'.self::$cacheVersionSite,$cacheModules);

            return $fileCompiled;
        }

    }

    public static function getModule($module){

        $cfg = erConfigClassLhConfig::getInstance();
        self::$moduleCacheEnabled = $cfg->getSetting( 'site', 'modulecompile' );
        
        // Because each siteaccess can have different extension cache key has to have this
        $siteAccess = erLhcoreClassSystem::instance()->SiteAccess;
                
        if ( self::$cacheInstance === null ) {
        	self::$cacheInstance = CSCacheAPC::getMem();
        }

        if (self::$moduleCacheEnabled === true) {
            if ( ($cacheModules = self::$cacheInstance->restore('moduleFunctionsCache_'.$module.'_'.$siteAccess.'_version_'.self::$cacheVersionSite)) !== false)
            {
            	return $cacheModules;
            }

            $cacheWriter = new erLhcoreClassCacheStorage('cache/cacheconfig/');
            if ( ($cacheModules = $cacheWriter->restore('moduleFunctionsCache_'.$module.'_'.$siteAccess)) == false)
            {
            	$cacheModules = array();
            }

            if (is_countable($cacheModules) and count($cacheModules) > 0) {
                self::$cacheInstance->store('moduleFunctionsCache_'.$module.'_'.$siteAccess.'_version_'.self::$cacheVersionSite,$cacheModules);
                return $cacheModules;
            }
        }

        $extensions = $cfg->getOverrideValue('site','extensions');

        $ViewListCompiled = array();

        // Is it core module
        if (file_exists('modules/lh'.$module.'/module.php'))
        {
            include('modules/lh'.$module.'/module.php');

            foreach ($ViewList as $view => $params){
                $ViewList[$view]['script_path'] = 'modules/lh'.$module.'/'.$view.'.php';
            }

            $ViewListCompiled = array_merge($ViewListCompiled,$ViewList);
        }

        $extensionViewList = array();

        // Is it extension module
        foreach ($extensions as $extension)
        {
            if (file_exists('extension/'.$extension.'/modules/lh'.$module.'/module.php')){

                include('extension/'.$extension.'/modules/lh'.$module.'/module.php');

                foreach ($ViewList as $view => $params){
                    if (!in_array($view,$extensionViewList)){
                        $ViewList[$view]['script_path'] = 'extension/'.$extension.'/modules/lh'.$module.'/'.$view.'.php';
                        $extensionViewList[] = $view;
                    } else {
                        unset($ViewList[$view]);
                    }
                }

                $ViewListCompiled = array_merge($ViewListCompiled,$ViewList);
             }
        }

        if (count($ViewListCompiled) > 0) {
            if (self::$moduleCacheEnabled === true) {
                $cacheWriter->store('moduleFunctionsCache_'.$module.'_'.$siteAccess,$ViewListCompiled);
                self::$cacheInstance->store('moduleFunctionsCache_'.$module.'_'.$siteAccess.'_version_'.self::$cacheVersionSite,$ViewListCompiled);
            }
            return $ViewListCompiled;
        }

        // Module does not exists
        return false;

    }

    public static function moduleInit($params = array())
    {        
        $cfg = erConfigClassLhConfig::getInstance();
        
        self::$debugEnabled = $cfg->getSetting('site', 'debug_output');
        
        // Enable errors output before extensions intialization
        if (self::$debugEnabled == true) {
            @ini_set('error_reporting', E_ALL);
            @ini_set('display_errors', 1);
        }

        self::$cacheInstance = CSCacheAPC::getMem();
        self::$cacheVersionSite = self::$cacheInstance->getCacheVersion('site_version');
        self::$defaultTimeZone = $cfg->getSetting('site', 'time_zone', false);
        self::$dateFormat = $cfg->getSetting('site', 'date_format', false);
        self::$dateHourFormat = $cfg->getSetting('site', 'date_hour_format', false);
        self::$dateDateHourFormat = $cfg->getSetting('site', 'date_date_hour_format', false);
        
        $url = erLhcoreClassURL::getInstance();
        
        if (!isset($params['ignore_extensions'])){
            // Attatch extension listeners
            self::attatchExtensionListeners();
        }

        self::$currentModuleName = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string)$url->getParam( 'module' ));
        self::$currentView = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string)$url->getParam( 'function' ));

        if (self::$currentModuleName == '' || (self::$currentModule = self::getModule(self::$currentModuleName)) === false) {
            $params = $cfg->getOverrideValue('site','default_url');

            if (self::$currentModuleName != '') {
            	header("HTTP/1.1 301 Moved Permanently");
            	self::redirect();
            	exit;
            }

            self::$currentView = $params['view'];
            self::$currentModuleName = $params['module'];
            
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.core.default_url', array('url' => & $url));
            
            self::$currentModule = self::getModule(self::$currentModuleName);
        }

        return self::runModule();
    }

    public static function setView($view) {
        self::$currentView = $view;
    }
    
    public static function setModule($module) {
        self::$currentModuleName = $module;
    }
            
    static function redirect($url = '/', $appendURL = '')
    {
        header('Location: '. erLhcoreClassDesign::baseurl($url).$appendURL );
    }

    public static function getModuleName() {
        return self::$currentModuleName;
    }

    public static function getCurrentView() {
        return self::$currentView;
    }

    static private $currentModule = NULL;
    static private $currentModuleName = NULL;
    static private $currentView = NULL;

    static private $moduleCacheEnabled = NULL;
    static private $cacheInstance = NULL;
    static private $cacheVersionSite = NULL;
    static private $debugEnabled = false;
    
    static private $extensionsBootstraps = array();
    
    // Should we cache cache config variables
    // Instance version of chat should not cache, because each customer can have a different one
    public static $cacheDbVariables = true;    
    
    public static $defaultTimeZone = NULL;
    public static $dateFormat = NULL;
    public static $dateHourFormat = NULL;
    public static $dateDateHourFormat = NULL;
}

?>
