<?php

class erConfigClassLhCacheConfig
{
    private static $instance = null;
    public $conf;
    
    private $expireOptions = array('translationfile','accessfile');
    private $sessionExpireOptions = array('access_array','lhCacheUserDepartaments');
    
    public function __construct()
    {
        $sys = erLhcoreClassSystem::instance()->SiteDir;
        
        $ini = new ezcConfigurationArrayReader($sys . '/cache/cacheconfig/settings.ini.php' );
        if ( $ini->configExists() )
        {
            $this->conf = $ini->load();
        } else {
           
        }
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
        $sys = erLhcoreClassSystem::instance()->SiteDir;    
            
        $writer = new ezcConfigurationArrayWriter($sys . 'cache/cacheconfig/settings.ini.php');        
        $writer->setConfig( $this->conf );
        $writer->save();
    }
    
    public function expireCache()
    {
        foreach ($this->expireOptions as $option)
        {
            $this->conf->setSetting( 'cachetimestamps', $option, 0);
        }  
        
        foreach ($this->sessionExpireOptions as $option)
        {
            if (isset($_SESSION[$option])) unset($_SESSION[$option]);
        }
        
        $this->save();       
    }
    
    
}


?>