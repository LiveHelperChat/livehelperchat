<?php

class erConfigClassLhCacheConfig
{
    private static $instance = null;
    public $conf;

    private $expireOptions = array('translationfile','accessfile');
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

    public function getSetting($section, $key)
    {
        if (isset($this->conf['settings'][$section][$key])) {
            return $this->conf['settings'][$section][$key];
        } else {
            throw new Exception('Setting with section {'.$section.'} value {'.$key.'} was not found');
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
	        file_put_contents('cache/cacheconfig/settings.ini.new.php',"<?php\n return ".var_export($this->conf,true).";\n?>");
	        // Atomic operation
	        rename('cache/cacheconfig/settings.ini.new.php','cache/cacheconfig/settings.ini.php');
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
	
	        foreach ($this->sessionExpireOptions as $option)
	        {
	            if (isset($_SESSION[$option])) unset($_SESSION[$option]);
	        }
	
	        $compiledModules = ezcBaseFile::findRecursive( 'cache/cacheconfig',array( '@\.cache\.php@' ) );
	        foreach ($compiledModules as $compiledClass)
			{
			    unlink($compiledClass);
			}
	
			$compiledTemplates = ezcBaseFile::findRecursive( 'cache/compiledtemplates',array( '@(\.php)@' ) );
	
			foreach ($compiledTemplates as $compiledTemplate)
			{
				unlink($compiledTemplate);
			}
			
			
			$compiledTemplates = ezcBaseFile::findRecursive( 'cache/compiledtemplates',array( '@(\.js|\.css)@' ) );
	
			foreach ($compiledTemplates as $compiledTemplate)
			{
			    if ($forceClean == true || filemtime($compiledTemplate) < time()-24*3600) {
			        unlink($compiledTemplate);
			    }
			}
						
			$instance = CSCacheAPC::getMem();
			$instance->increaseImageManipulationCache();
	
	        $this->save();
    	}
    }


}


?>