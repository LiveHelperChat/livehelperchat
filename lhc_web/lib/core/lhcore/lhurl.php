<?php


class erLhcoreClassURL extends ezcUrl {

    private static $instance = null;

    private $requireSiteAccess = false;
    private $baseUrl = null;
    
    public function __construct($urlString, $urlCfgDefault)
    {
        parent::__construct($urlString, $urlCfgDefault);
    }

    public static function resetInstance(){
        self::$instance = null;
    }
    
    public function setCoreURL($url)
    {
        $sysConfiguration = erLhcoreClassSystem::instance();
        $sysConfiguration->RequestURI = ($this->requireSiteAccess == true ? $sysConfiguration->SiteAccess : '') . '/' . $url;
        
        $urlInstance = self::getInstance();
        $urlInstance->parseUrl($urlInstance->baseUrl .   $sysConfiguration->RequestURI);        
    }
    
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            $sysConfiguration = erLhcoreClassSystem::instance();

            $urlCfgDefault = ezcUrlConfiguration::getInstance();
            $urlCfgDefault->basedir = $sysConfiguration->WWWDir;
            $urlCfgDefault->script  = $sysConfiguration->IndexFile;
            $urlCfgDefault->unorderedDelimiters = array( '(', ')' );
            $urlCfgDefault->orderedParameters = array();
            $urlCfgDefault->unorderedParameters = array();
            $urlCfgDefault->addOrderedParameter( 'siteaccess' );
            $urlCfgDefault->addOrderedParameter( 'module' );
            $urlCfgDefault->addOrderedParameter( 'function' );
            
            $cfgSite = erConfigClassLhConfig::getInstance();
            
            $baseUrl =  ($cfgSite->getSetting( 'site', 'force_virtual_host', false) === false ? 'index.php' : '');
            
            $urlInstance = new erLhcoreClassURL($baseUrl . $sysConfiguration->RequestURI, $urlCfgDefault);
            $urlInstance->baseUrl = $baseUrl;
            
            $siteaccess = $urlInstance->getParam( 'siteaccess' );

            $availableSiteaccess = $cfgSite->getSetting( 'site', 'available_site_access' );
            $defaultSiteAccess = $cfgSite->getSetting( 'site', 'default_site_access' );
           
            if ($defaultSiteAccess != $siteaccess && in_array($siteaccess,$availableSiteaccess))
            {
                $optionsSiteAccess = $cfgSite->getSetting('site_access_options',$siteaccess);
                $sysConfiguration->Language = $siteaccess == 'site_admin' ? erLhcoreClassModelUserSetting::getSetting('user_language',$optionsSiteAccess['locale'],false,true) : $optionsSiteAccess['locale'];
                $sysConfiguration->ThemeSite = $optionsSiteAccess['theme'];
                $sysConfiguration->ContentLanguage = $optionsSiteAccess['content_language'];
                
                $sysConfiguration->WWWDirLang = '/'.$siteaccess;
                $sysConfiguration->SiteAccess = $siteaccess;
                                                
                if ($optionsSiteAccess['locale'] != 'en_EN')
                {
                    $urlInstance->setParam('module',$urlInstance->getParam( 'module' ));
                    $urlInstance->setParam('function',$urlInstance->getParam( 'function' ));
                }
                               
                if (isset($_POST['switchLang']) && in_array($_POST['switchLang'], $availableSiteaccess)){                	                	
                	$optionsSiteAccessOverride = $cfgSite->getSetting('site_access_options', $_POST['switchLang']);
                	$sysConfiguration->Language = $optionsSiteAccessOverride['locale'];                	
                	$sysConfiguration->SiteAccess = $_POST['switchLang'];
		            if ($defaultSiteAccess != $sysConfiguration->SiteAccess) {
		               	$sysConfiguration->WWWDirLang = '/'.$sysConfiguration->SiteAccess;
		            } else {
		               	$sysConfiguration->WWWDirLang = '';
		            }
                }
                
                $urlInstance->requireSiteAccess = true;
                
            } else {

                $optionsSiteAccess = $cfgSite->getSetting('site_access_options',$defaultSiteAccess);

                // Falling back
                $sysConfiguration->SiteAccess = $defaultSiteAccess;
                $sysConfiguration->Language = $siteaccess == 'site_admin' ? erLhcoreClassModelUserSetting::getSetting('user_language',$optionsSiteAccess['locale'],false,true) : $optionsSiteAccess['locale'];
                $sysConfiguration->ThemeSite = $optionsSiteAccess['theme'];
                $sysConfiguration->ContentLanguage = $optionsSiteAccess['content_language'];

                if (isset($_POST['switchLang']) && in_array($_POST['switchLang'], $availableSiteaccess)){
                	$optionsSiteAccessOverride = $cfgSite->getSetting('site_access_options',$_POST['switchLang']);
                	$sysConfiguration->Language = $optionsSiteAccessOverride['locale'];
                	$sysConfiguration->SiteAccess = $_POST['switchLang'];                	
                	if ($defaultSiteAccess != $sysConfiguration->SiteAccess) {                		
                		$sysConfiguration->WWWDirLang = '/'.$sysConfiguration->SiteAccess; 
                	}               	         	
                }
                
                if ($siteaccess !== $defaultSiteAccess)
                {
                    // Just in case first url part consists of two letter, let's assume it's language argument and keep everytying as is
                    if (strlen($siteaccess) != 2)
                    {
                        // To reset possition counter
                        $urlCfgDefault->removeOrderedParameter('siteaccess');
                        $urlCfgDefault->removeOrderedParameter('module');
                        $urlCfgDefault->removeOrderedParameter('function');
                    
                        // Reinit parameters
                        $urlCfgDefault->addOrderedParameter( 'module' );
                        $urlCfgDefault->addOrderedParameter( 'function' );
                    
                        //Apply default configuration
                        $urlInstance->applyConfiguration($urlCfgDefault);
                    
                        if ($optionsSiteAccess['locale'] != 'en_EN')
                        {
                            $urlInstance->setParam('module',$urlInstance->getParam( 'module' ));
                            $urlInstance->setParam('function',$urlInstance->getParam( 'function' ));
                        }
                    }
                }
            }

            self::$instance =  $urlInstance;
        }
        return self::$instance;
    }

    public static function getTranslatedURL($url, $suburl = '')
    {
    	$cache = CSCacheAPC::getMem();

    	$cacheKey = md5('site_version_'.$cache->getCacheVersion('site_version').'_alias_'.$url.'_'.$suburl);

    	if (($returnAlias = $cache->restore($cacheKey)) === false)
    	{
    		$url = erLhcoreClassCharTransform::TransformToURL($url);
    		$returnAlias = false;

    		if ( $returnAlias !== false ) {
    			$cache->store($cacheKey,$returnAlias);
    		}
    	}

    	return $returnAlias;
    }

    const TRANSLATION_ARTICLE = 0;
    const TRANSLATION_URLALIAS = 1;

    public static function translatePath(& $partsArray, $arrayReplaces)
    {
    	foreach ($partsArray as $key => & $value)
    	{
    		if ( key_exists($key,$arrayReplaces) ) {
    			$value = $arrayReplaces[$key];
    		}
    	}
    }

}
?>