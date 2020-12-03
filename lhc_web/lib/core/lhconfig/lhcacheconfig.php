<?php

class erConfigClassLhCacheConfig
{
    private static $instance = null;
    public $conf;

    private $expireOptions = array('accessfile');
    private $sessionExpireOptions = array('access_array','lhCacheUserDepartaments');

    private $expiredInRuntime = false;

    public function __construct()
    {
    	$this->conf = @include('cache/cacheconfig/settings.ini.php');
        if ( !is_array($this->conf) ) {
        	// Restore default settings if error accours
        	$this->conf = array (
			  'settings' => 
			  array (
			    'cachetimestamps' => 
			    array (
			      'translationfile' => 0,
			      'accessfile' => 0,
			    ),
			  ),
			  'comments' => NULL,
			);
        }
    }

    public function getSetting($section, $key, $throwException = true)
    {
        if (isset($this->conf['settings'][$section][$key])) {
            return $this->conf['settings'][$section][$key];
        } else {
            if ($throwException == true) {
                throw new Exception('Setting with section {'.$section.'} value {'.$key.'} was not found');
            } else {
                return null;
            }
        }
    }

    public function hasSetting($section, $key)
    {
        return isset($this->conf['settings'][$section][$key]);
    }

    public function setSetting($section, $key, $value)
    {
        $this->conf['settings'][$section][$key] = $value;
    }

    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new erConfigClassLhCacheConfig();
        }
        return self::$instance;
    }

    public function save()
    {
    	// Save only if array
    	if (is_array($this->conf) ) {
	        file_put_contents('cache/cacheconfig/settings.ini.new.php',"<?php\n return ".var_export($this->conf,true).";\n?>",LOCK_EX);
	        // Atomic operation
            if (file_exists('cache/cacheconfig/settings.ini.new.php')) {
                @rename('cache/cacheconfig/settings.ini.new.php','cache/cacheconfig/settings.ini.php');
            }
    	}
    }

    public function setExpiredInRuntime($expired)
    {
    	$this->expiredInRuntime = $expired;
    }
    
    public function expireCache($forceClean = false)
    {
        if (isset($_SESSION['lhc_chat_config'])) {
            unset($_SESSION['lhc_chat_config']);
        }
        
    	if ($this->expiredInRuntime == false) {
    		$this->expiredInRuntime = true;

	        foreach ($this->expireOptions as $option)
	        {
	            $this->setSetting( 'cachetimestamps', $option, 0);
	        }

	        if (file_exists('settings/settings.ini.php')) {
	            $settings = include 'settings/settings.ini.php';
	            foreach ($settings['settings']['site_access_options'] as $siteAccess) {
                    $this->setSetting( 'cachetimestamps', 'translationfile_' . $siteAccess['locale'], 0);
                }
            }

	        foreach ($this->sessionExpireOptions as $option)
	        {
	            if (isset($_SESSION[$option])) unset($_SESSION[$option]);
	        }
	
	        $compiledModules = ezcBaseFile::findRecursive( 'cache/cacheconfig',array( '@\.cache\.php@' ) );
	        foreach ($compiledModules as $compiledClass)
			{
                if (file_exists($compiledClass)) {
                    @unlink($compiledClass);
                }
			}
	
			$compiledTemplates = ezcBaseFile::findRecursive( 'cache/compiledtemplates',array( '@(\.php)@' ) );
	
			foreach ($compiledTemplates as $compiledTemplate)
			{
			    if (file_exists($compiledTemplate)) {
                    @unlink($compiledTemplate);
                }
			}
			
			
			$compiledTemplates = ezcBaseFile::findRecursive( 'cache/compiledtemplates',array( '@(\.js|\.css)@' ) );
	
			foreach ($compiledTemplates as $compiledTemplate)
			{
			    if (file_exists($compiledTemplate) && ($forceClean == true || filemtime($compiledTemplate) < time()-24*3600)) {
			        @unlink($compiledTemplate);
			    }
			}
						
			$instance = CSCacheAPC::getMem();
			$instance->increaseImageManipulationCache();
	
	        $this->save();
    	}
    }


}


?>