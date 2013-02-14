<?php

class erConfigClassLhCacheConfig
{
    private static $instance = null;
    public $conf;
    
    private $expireOptions = array('translationfile','accessfile');
    private $sessionExpireOptions = array('access_array','lhCacheUserDepartaments');

    
    public function __construct()
    {      
        $this->conf = include('cache/cacheconfig/settings.ini.php');        
    }
    
    public function getSetting($section, $key)
    {
        if (isset($this->conf['settings'][$section][$key])) {
            return $this->conf['settings'][$section][$key];
        } else {
            throw new Exception('Setting with section {'.$section.'} value {'.$key.'}');
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
        file_put_contents('cache/cacheconfig/settings.ini.new.php',"<?php\n return ".var_export($this->conf,true).";\n?>");     
        // Atomic operation   
        rename('cache/cacheconfig/settings.ini.new.php','cache/cacheconfig/settings.ini.php');
    }
    
    public function expireCache()
    {
        foreach ($this->expireOptions as $option)
        {
            $this->setSetting( 'cachetimestamps', $option, 0);
        }  
        
        foreach ($this->sessionExpireOptions as $option)
        {
            if (isset($_SESSION[$option])) unset($_SESSION[$option]);
        }
        
        $compiledModules = ezcBaseFile::findRecursive( 'cache/cacheconfig',array( '@\.cache@' ) );        
        foreach ($compiledModules as $compiledClass)
		{
		    unlink($compiledClass);
		}
				
		$compiledTemplates = ezcBaseFile::findRecursive( 'cache/compiledtemplates',array( '@(\.php|\.js|\.css)@' ) );
		
		foreach ($compiledTemplates as $compiledTemplate)
		{
			unlink($compiledTemplate);
		}		
		
		$instance = CSCacheAPC::getMem(); 
		$instance->increaseImageManipulationCache();
						
        $this->save();       
    }
    
    
}


?>