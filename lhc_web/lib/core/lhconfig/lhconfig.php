<?php

class erConfigClassLhConfig
{
    private static $instance = null;
    public $conf;
    
    public function __construct()
    {
        $sys = erLhcoreClassSystem::instance()->SiteDir;
        
        $ini = new ezcConfigurationArrayReader($sys . '/settings/settings.ini.php' );
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
            self::$instance = new erConfigClassLhConfig();            
        }
        return self::$instance;
    }
    
    public function save()
    {
        $sys = erLhcoreClassSystem::instance()->SiteDir;    
            
        $writer = new ezcConfigurationArrayWriter($sys . 'settings/settings.ini.php');        
        $writer->setConfig( $this->conf );
        $writer->save();
    }
    
}


?>