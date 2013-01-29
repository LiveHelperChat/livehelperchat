<?php

class erLhcoreClassModule{
    
    static function runModule()
    {
        if (isset(self::$currentModule[self::$currentView]))
        {            
            // Just to start session
            $currentUser = erLhcoreClassUser::instance();
                
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
                if (!$currentUser->hasAccessTo('lh'.self::$currentModuleName,$Params['module']['functions']))
                {
                   self::redirect('user/login');
                   exit;
                }
            }
                      
            if (isset($Params['module']['limitations']))
            {       
                $access = call_user_func($Params['module']['limitations']['self']['method'],$Params['user_parameters'][$Params['module']['limitations']['self']['param']],$currentUser->hasAccessTo('lh'.self::$currentModuleName,$Params['module']['limitations']['global']));               	
                
                if ($access == false) {                
                	include_once('modules/lhkernel/nopermissionobject.php');  
                   	return $Result;
                } else {
                	$Params['user_object'] = $access;
                }                                
            }
                        
            include_once(self::getModuleFile(self::$currentModuleName,self::$currentView)); 
                        
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
    
    public static function getModuleDefaultView($module)
    {
        $cfg = erConfigClassLhConfig::getInstance();
        $extensions = $cfg->getSetting('site','extensions');
                      
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
    
    public static function getModuleFile() {
        
        $cfg = erConfigClassLhConfig::getInstance();
        $cacheEnabled = $cfg->getSetting( 'site', 'modulecompile' );
        
        if ($cacheEnabled === false) {
            return self::$currentModule[self::$currentView]['script_path'];
        } else {
                                                     
            $instance = erLhcoreClassSystem::instance();
            $cacheKey = md5(self::$currentModuleName.'_'.self::$currentView.'_'.$instance->WWWDirLang);
            
            if ( ($cacheModules = self::$cacheInstance->restore('moduleCache_'.self::$currentModuleName.'_version_'.self::$cacheVersionSite)) !== false && key_exists($cacheKey,$cacheModules))
            {
            	return $cacheModules[$cacheKey];
            }
                        
            $cacheWriter = new ezcCacheStorageFileArray(erLhcoreClassSystem::instance()->SiteDir . 'cache/cacheconfig/');            
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

                       
            $fileCompiled = $instance->SiteDir . 'cache/compiledtemplates/'.md5($file.$instance->WWWDirLang).'.php';
            
            $Matches = array();
			preg_match_all('/erTranslationClassLhTranslation::getInstance\(\)->getTranslation\(\'(.*?)\',\'(.*?)\'\)/i',$contentFile,$Matches);					
			foreach ($Matches[1] as $key => $TranslateContent)
			{	
				$contentFile = str_replace($Matches[0][$key],'\''.erTranslationClassLhTranslation::getInstance()->getTranslation($TranslateContent,$Matches[2][$key]).'\'',$contentFile);	
			}
              
			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::baseurl\((.*?)\)/i',$contentFile,$Matches); 
			foreach ($Matches[1] as $key => $UrlAddress)
			{		
				$contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::baseurl(trim($UrlAddress,'\'')).'\'',$contentFile);	
			} 
			 
			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::baseurldirect\((.*?)\)/i',$contentFile,$Matches); 
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
			
			// Compile config settings
            $Matches = array();
            preg_match_all('/erLhcoreClassModelSystemConfig::fetch\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->current_value/i',$contentFile,$Matches); 
            foreach ($Matches[1] as $key => $UrlAddress)
            {
                $valueConfig = erLhcoreClassModelSystemConfig::fetch($Matches[2][$key])->current_value;
                $valueReplace = '';
                $valueReplace = '\''.str_replace("'","\'",$valueConfig).'\'';
                $contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);                               
            }                        
                        
			file_put_contents($fileCompiled,$contentFile);
            
			$cacheModules[$cacheKey] = $fileCompiled;		
					
			$cacheWriter->store('moduleCache_'.self::$currentModuleName,$cacheModules);
			self::$cacheInstance->store('moduleCache_'.self::$currentModuleName.'_version_'.self::$cacheVersionSite,$cacheModules);
					
            return $fileCompiled;
        }
        
    }
   
    
    public static function getModule($module){
           
        $cfg = erConfigClassLhConfig::getInstance();               
        self::$moduleCacheEnabled = $cfg->getSetting( 'site', 'modulecompile' );
        
        if ( self::$cacheInstance === null ) {
        	self::$cacheInstance = CSCacheAPC::getMem();
        }
        
        if (self::$moduleCacheEnabled === true) { 
            if ( ($cacheModules = self::$cacheInstance->restore('moduleFunctionsCache_'.$module.'_version_'.self::$cacheVersionSite)) !== false)
            {     
            	return $cacheModules;
            }
                     
            $cacheWriter = new ezcCacheStorageFileArray(erLhcoreClassSystem::instance()->SiteDir . 'cache/cacheconfig/');
            if ( ($cacheModules = $cacheWriter->restore('moduleFunctionsCache_'.$module)) == false)
            {        	
            	//$cacheWriter->store('moduleFunctionsCache_'.$module,array());
            	$cacheModules = array();
            }
                            
            if (count($cacheModules) > 0){
                self::$cacheInstance->store('moduleFunctionsCache_'.$module.'_version_'.self::$cacheVersionSite,$cacheModules);
                return $cacheModules;
            }
        }  
         
        
         
        $extensions = $cfg->getSetting('site','extensions');
        
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
        
        // Is it extension module        
        foreach ($extensions as $extension)
        {   
            if (file_exists('extension/'.$extension.'/modules/lh'.$module.'/module.php')){
              
                include('extension/'.$extension.'/modules/lh'.$module.'/module.php');
                
                foreach ($ViewList as $view => $params){
                    $ViewList[$view]['script_path'] = 'extension/'.$extension.'/modules/lh'.$module.'/'.$view.'.php';
                }                          
                
                $ViewListCompiled = array_merge($ViewListCompiled,$ViewList);
             }
        }
                       
        if (count($ViewListCompiled) > 0) {
            if (self::$moduleCacheEnabled === true) { 
                $cacheWriter->store('moduleFunctionsCache_'.$module,$ViewListCompiled);
                self::$cacheInstance->store('moduleFunctionsCache_'.$module.'_version_'.self::$cacheVersionSite,$ViewListCompiled);
            }            
            return $ViewListCompiled;
        }               
        
        // Module does not exists
        return false;
        
    }

    public static function moduleInit()
    {                 
        $url = erLhcoreClassURL::getInstance();
        $cfg = erConfigClassLhConfig::getInstance();

        self::$currentModuleName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $url->getParam( 'module' ));
        self::$currentView = preg_replace('/[^a-zA-Z0-9\-_]/', '', $url->getParam( 'function' ));

        self::$cacheInstance = CSCacheAPC::getMem();
        self::$cacheVersionSite = self::$cacheInstance->getCacheVersion('site_version');

        if (self::$currentModuleName == '' || (self::$currentModule = self::getModule(self::$currentModuleName)) === false) {        	
            $params = $cfg->getOverrideValue('site','default_url');

            if (self::$currentModuleName != '') {
            	header("HTTP/1.1 301 Moved Permanently");
            	self::redirect();
            	exit;
            }

            self::$currentView = $params['view'];
            self::$currentModuleName = $params['module'];
            self::$currentModule = self::getModule(self::$currentModuleName);
        }
        
        return self::runModule();
        
    }
    
    
    static function redirect($url = '/', $appendURL = '')
    {
        header('Location: '. erLhcoreClassDesign::baseurl($url).$appendURL );
    }
    
    static private $currentModule = NULL;
    static private $currentModuleName = NULL;
    static private $currentView = NULL;
    static private $moduleCacheEnabled = NULL;
    static private $cacheInstance = NULL;
    static private $cacheVersionSite = NULL;
}

?>