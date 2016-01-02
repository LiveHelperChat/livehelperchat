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
            $this->translationFileModifyTime = filemtime($sys . '/translations/' . $this->languageCode . '/translation.ts');

            try {
	            if ($cfg->getSetting( 'cachetimestamps', 'translationfile' ) != $this->translationFileModifyTime)
	            {
	                $this->updateCache();
	                $cfg->setSetting( 'cachetimestamps', 'translationfile', $this->translationFileModifyTime);
	                $cfg->save();
	            }
            } catch (Exception $e) {
            	$this->updateCache();
            	$cfg->setSetting( 'cachetimestamps', 'translationfile', $this->translationFileModifyTime);
            	$cfg->save();
            }
            
            $this->cacheObj = new ezcCacheStorageFileArray( $sys . '/cache/translations' );
            $this->backend = new ezcTranslationCacheBackend( $this->cacheObj );
            $this->manager = new ezcTranslationManager( $this->backend );
        }
    }
    
    public function initLanguage() {
    	$sys = erLhcoreClassSystem::instance()->SiteDir;    
    	$this->languageCode = erLhcoreClassSystem::instance()->Language;
    	 
    	$cfg = erConfigClassLhCacheConfig::getInstance();
    	if ($this->languageCode != 'en_EN')
    	{
    		$this->translationFileModifyTime = filemtime($sys . '/translations/' . $this->languageCode . '/translation.xml');
    
    		if ($cfg->getSetting( 'cachetimestamps', 'translationfile' ) != $this->translationFileModifyTime)
    		{
    			$this->updateCache();
    			$cfg->setSetting( 'cachetimestamps', 'translationfile', $this->translationFileModifyTime);
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
            return htmlspecialchars($this->insertarguments($string,$params),ENT_QUOTES);
        }

        try {
           $context = $this->manager->getContext( $this->languageCode, $context );
           try {
                $translated = $context->getTranslation($string, $params);

                if ($translated == '') return htmlspecialchars($this->insertarguments($string, $params),ENT_QUOTES);

                return htmlspecialchars($translated,ENT_QUOTES);

           } catch (Exception $e){
                return htmlspecialchars($this->insertarguments($string, $params),ENT_QUOTES);
           }

        } catch (Exception $e) {

            $this->updateCache();
            try {
                $translated = $this->translateFromXML($context,$string,$params);
            } catch (Exception $e){
                $translated = $this->insertarguments($string, $params);
            }

            return htmlspecialchars($translated,ENT_QUOTES);
        }
    }

    private function updateCache(){

    	try {
	        $sys = erLhcoreClassSystem::instance()->SiteDir;
	        $reader = new ezcTranslationTsBackend( $sys . '/translations/' . $this->languageCode );
	        $reader->setOptions( array( 'format' => 'translation.ts' ) );
	        $reader->initReader( $this->languageCode );

	        $cacheObj = new ezcCacheStorageFileArray( $sys . '/cache/translations' );
	        $writer = new ezcTranslationCacheBackend( $cacheObj );
	        $writer->initWriter( $this->languageCode );

	        // Load extensions translations
	        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue( 'site', 'extensions' );
	        $contextDataArray = array();
	        foreach ($extensions as $ext) {
	        	$trsDir = $sys . 'extension/' . $ext . '/translations/' . $this->languageCode .  '/translation.ts';
	        	if (file_exists($trsDir)) {	        		
        			$readerExtension = new ezcTranslationTsBackend( $sys . '/extension/'.$ext.'/translations/' . $this->languageCode );
        			$readerExtension->setOptions( array( 'format' => 'translation.ts' ) );
        			$readerExtension->initReader( $this->languageCode );	        
        			foreach ( $readerExtension as $contextName => $contextData )
        			{	  
        				if (isset($contextDataArray[$contextName])) { // Perhaps few extensions have same content?
        					$contextDataArray[$contextName] = array_merge($contextDataArray[$contextName],$contextData);
        				} else {				
        					$contextDataArray[$contextName] = $contextData;
        				}
        			}	        
        			$readerExtension->deInitReader();	        		
	        	}
	        }
	        
	     	// Store translations
	        foreach ( $reader as $contextName => $contextData )
	        {	        	
	        	if (isset($contextDataArray[$contextName])) {
	        		$contextData = array_merge($contextData,$contextDataArray[$contextName]);
	        		unset($contextDataArray[$contextName]);
	        	};	        	
	            $writer->storeContext( $contextName, $contextData );
	        }
	        
	        // Store unique extension context
	        foreach ($contextDataArray as $contextName => $contextData){
	        	$writer->storeContext( $contextName, $contextData );
	        }
	        
	        $reader->deInitReader();
               
	        unset($contextDataArray);	       
	        $writer->deInitWriter();
    	} catch (Exception $e) { // Sometimes write fails, so ignore it

    	}
    }

    private function translateFromXML($context,$string,$params = array())
    {
        $sys = erLhcoreClassSystem::instance()->SiteDir;

        $reader = new ezcTranslationTsBackend( $sys . '/translations/' . $this->languageCode );
        $reader->setOptions( array( 'format' => 'translation.ts' ) );

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