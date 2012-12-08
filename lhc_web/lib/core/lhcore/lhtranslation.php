<?php

class erTranslationClassLhTranslation
{
    private static $instance = null;
    public $cacheObj;
    public $backend;
    public $manager;
    
    private $languageCode;
    private $translationFileModifyTime;
    
    public function __construct()
    {
        $sys = erLhcoreClassSystem::instance()->SiteDir;
            
        $this->languageCode = erLhcoreClassSystem::instance()->Language;
               
        $cfg = erConfigClassLhCacheConfig::getInstance();
        if ($this->languageCode != 'en_EN')
        {        
            $this->translationFileModifyTime = filemtime($sys . '/translations/' . $this->languageCode . '/translation.xml');
            
            if ($cfg->conf->getSetting( 'cachetimestamps', 'translationfile' ) != $this->translationFileModifyTime)
            {
                $this->updateCache();
                $cfg->conf->setSetting( 'cachetimestamps', 'translationfile', $this->translationFileModifyTime);
                $cfg->save();
            }
            
            $this->cacheObj = new ezcCacheStorageFileArray( $sys . '/cache/translations' );
            $this->backend = new ezcTranslationCacheBackend( $this->cacheObj );        
            $this->manager = new ezcTranslationManager( $this->backend );
        }
    }
    
    
    /**
     * Taken from ez4
     * */
    function insertarguments( $text, $arguments )
    {
        if ( count( $arguments ) > 0 )
        {
            $replaceList = array();
            foreach ( $arguments as $argumentKey => $argumentItem )
            {
                if ( is_int( $argumentKey ) )
                    $replaceList['%' . ( ($argumentKey%9) + 1 )] = $argumentItem;
                else
                    $replaceList['%' .$argumentKey] = $argumentItem;
            }
            $text = strtr( $text, $replaceList );
        }
        return $text;
    }


    public function getTranslation($context, $string, $params = array())
    {
        if ($this->languageCode == 'en_EN') { 
            return $this->insertarguments($string,$params);
        }
         
        try {
           $context = $this->manager->getContext( $this->languageCode, $context );
           try {     
              
                return  $context->getTranslation($string, $params);
           } catch (Exception $e){
                return $string;
           }
           
        } catch (Exception $e) {    
            $this->updateCache();
            return $this->translateFromXML($context,$string,$params);
        }
    }
    
    private function updateCache(){
        
        $sys = erLhcoreClassSystem::instance()->SiteDir;
        $reader = new ezcTranslationTsBackend( $sys . '/translations/' . $this->languageCode );
        $reader->setOptions( array( 'format' => 'translation.xml' ) );
        $reader->initReader( $this->languageCode );
        
        $cacheObj = new ezcCacheStorageFileArray( $sys . '/cache/translations' );
        $writer = new ezcTranslationCacheBackend( $cacheObj );
        $writer->initWriter( $this->languageCode );
        
        foreach ( $reader as $contextName => $contextData )
        {
            $writer->storeContext( $contextName, $contextData );
        }
        
        $reader->deInitReader();
        $writer->deInitWriter();       
    }
    
    private function translateFromXML($context,$string,$params = array())
    {
        $sys = erLhcoreClassSystem::instance()->SiteDir;
        
        $reader = new ezcTranslationTsBackend( $sys . '/translations/' . $this->languageCode );
        $reader->setOptions( array( 'format' => 'translation.xml' ) );
        
        $manager = new ezcTranslationManager( $reader );
        $ContextTranslation = $manager->getContext( $this->languageCode, $context );
             
        return $ContextTranslation->getTranslation( $string ,$params );
    }
    
    public static function getInstance()  
    {
        if ( is_null( self::$instance ) )
        {          
            self::$instance = new erTranslationClassLhTranslation();            
        }
        return self::$instance;
    }
    
}


?>