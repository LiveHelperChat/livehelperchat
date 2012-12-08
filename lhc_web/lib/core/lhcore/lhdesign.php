<?php



class erLhcoreClassDesign
{
    public static function design($path)
    {
        $cfg = erConfigClassLhConfig::getInstance();
        $instance = erLhcoreClassSystem::instance();
         
        return $instance->wwwDir() . '/design/'. $cfg->conf->getSetting( 'site', 'theme' ) .'/' . $path;
    } 
    
    public static function designtpl($path)   
    {
        $cfg = erConfigClassLhConfig::getInstance();
        $instance = erLhcoreClassSystem::instance();         
        return $instance->SiteDir .'/design/' . $cfg->conf->getSetting( 'site', 'theme' ) .  '/tpl/'. $path;
    }
    
    public static function baseurl($link = '')
    {
        $instance = erLhcoreClassSystem::instance();
                
        return $instance->WWWDir . '/index.php' .  $instance->WWWDirLang  . '/' . ltrim($link,'/');
    }
}


?>