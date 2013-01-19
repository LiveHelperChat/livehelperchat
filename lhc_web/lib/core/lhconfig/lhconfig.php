<?php

class erConfigClassLhConfig
{
    private static $instance = null;
    public $conf;
    
    public function __construct()
    {
         $this->conf = include('settings/settings.ini.php');        
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
    
    /**
     * This function should be used then value can be override by siteAccess
     * 
     * */
    public function getOverrideValue($section, $key)
    {
        $value = null;
        
        if ($this->hasSetting($section,$key))
        $value = $this->getSetting( $section, $key );
                
        $valueOverride = $this->getSetting('site_access_options',erLhcoreClassSystem::instance()->SiteAccess);
        
        if (key_exists($key,$valueOverride))  
              return $valueOverride[$key];
              
        return $value;
    }
    
    public static function getInstance()  
    {
        if ( is_null( self::$instance ) )
        {          
            self::$instance = new erConfigClassLhConfig();            
        }
        return self::$instance;
    }
    
    public function save()
    {
        file_put_contents('settings/settings.ini.php',"<?php\n return ".var_export($this->conf,true).";\n?>");
    }    
}


?>