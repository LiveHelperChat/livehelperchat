<?php


/**
 * Atomic cache writer
 * */
class erLhcoreClassCacheStorage {

	private $cacheDir = false;

	public function __construct($cacheDir) {
		if (is_writable($cacheDir)) {
			$this->cacheDir = $cacheDir;
		} else {
			throw new Exception("Directory {$cacheDir} not writable!");
		}
	}

	public function store($identifier, array $data) {
		try {
			// Temporary storage
			$fileName = $this->cacheDir . md5($identifier. time() . microtime() . rand(0, 1000)) . '.php';

			file_put_contents($fileName,"<?php\n return ".var_export($data,true).";\n?>");

			// Atomic operation
			rename($fileName,'cache/cacheconfig/'.$identifier.'.cache.php');

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function restore($identifier) {
		try {
			return @include ($this->cacheDir . $identifier.'.cache.php');
		} catch (Exception $e) {
			return false;
		}
	}
}


/**
 * Main part of code from :
 * http://www.massassi.com/php/articles/template_engines/
 *
 * Modified by remdex
 * */
class erLhcoreClassTemplate {
    private $vars = array(); /// Holds all the template variables

    private static $instance = null;
    private $cacheWriter = null;
    private $cacheTemplates = array() ;
    private $cacheEnabled = true;
    private $templatecompile = true;
	
    // Should we cache cache config variables
    // Instance version of chat should not cache, because each customer can have a different one
    public $cacheDbVariables = true;
    
    var $file = null;

    public static function getInstance($file = null)
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new erLhcoreClassTemplate($file);
        } else {
        	self::$instance->setFile($file);
        	self::$instance->vars = array();
        }

        return self::$instance;
    }

    public function enableCache($enable) {
        $this->templatecompile = $enable;
        $this->cacheEnabled = $enable;
    }
    
    /**
     * Constructor
     *
     * @param $file string the file name you want to load
     */
    function __construct($file = null) {

    	    	
        $cfg = erConfigClassLhConfig::getInstance();
        $this->cacheEnabled = $cfg->getSetting( 'site', 'templatecache' );
        $this->templatecompile = $cfg->getSetting( 'site', 'templatecompile' );

        if (!is_null($file))
        $this->file = $file;
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('tpl.new', array('tpl' => & $this));
                
        $cacheObj = CSCacheAPC::getMem();
        if (($this->cacheTemplates = $cacheObj->restore('templateCacheArray_version_'.$cacheObj->getCacheVersion('site_version'))) === false)
        {
            try {
            	$this->cacheWriter = new erLhcoreClassCacheStorage('cache/cacheconfig/');
            } catch (Exception $e) {

            	$instance = erLhcoreClassSystem::instance();
            	if ($instance->SiteAccess != 'site_admin')
            	{
            		// Perhaps user opened site without installing it first?
            		if ($cfg->getSetting( 'site', 'installed' ) == false) {
            			header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
            			exit;
            		}

            		header('HTTP/1.1 503 Service Temporarily Unavailable');
            		header('Status: 503 Service Temporarily Unavailable');
            		echo "<h1>Make sure cache/cacheconfig is writable by application</h1>";
            		exit;
            	} else {
            		throw $e;
            	}
            }


            if (($this->cacheTemplates = $this->cacheWriter->restore('templateCache')) == false)
            {
            	try {
            		$this->cacheWriter->store('templateCache',array());
            		$this->cacheTemplates = array();
            	} catch (Exception $e) {
            		// Do nothing
            	}

            	$cacheObj->store('templateCacheArray_version_'.$cacheObj->getCacheVersion('site_version'),array());
            }
        }
    }

    /**
     * Set a template variable.
     */
    function set($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * Set a template variables from array
     * */
    function setArray($array){
        $this->vars = array_merge($this->vars,$array);
    }


    /**
     * Set's template file
     * */
    function setFile($file)
    {
       $cfg = erConfigClassLhConfig::getInstance();
       $this->file = $file;
    }

    public static function strip_html($data)
	{
		$dataLines = explode("\n",$data);
		$return = "";
		foreach ($dataLines as $line)
		{
			if (($lineOutput = trim($line)) != ''){
				$return.= $lineOutput;
				if (preg_match('/(\/\/|<!--)/',$lineOutput)) // In case comment is at the end somewhere, /gallery/publicupload/
					$return.= "\n";
			}
		}

		// Spaces have to be adjusted using CSS
		$return=str_replace("> <","><",$return);

		// Need space some templates
		$return=str_replace('<?php','<?php ',$return);

	    return $return;
	}


    /**
     * Open, parse, and return the template file.
     *
     * @param $file string the template file name
     */
    function fetch($fileTemplate = null) {

    	$instance = erLhcoreClassSystem::instance();

    	$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;

    	if(!$fileTemplate) { $fileTemplate = $this->file; }

        if ($this->cacheEnabled == true && key_exists(md5($fileTemplate.$instance->WWWDirLang.$instance->Language.$port),$this->cacheTemplates))
        {
        	try {
        		return $this->fetchExecute($this->cacheTemplates[md5($fileTemplate.$instance->WWWDirLang.$instance->Language.$port)]);
        	} catch (Exception $e) {

        	}
        }

        $cfg = erConfigClassLhConfig::getInstance();
        $file = erLhcoreClassDesign::designtpl($fileTemplate);

        if ($this->templatecompile == true)
        {
	        $contentFile = php_strip_whitespace($file);

	        // Compile templates - 3 level of inclusions
	        for ($i = 0; $i < 9; $i++) {
    	        $Matches = array();
    			preg_match_all('/<\?php(\s*)include_once\(erLhcoreClassDesign::designtpl\(\'([a-zA-Z0-9-\.-\/\_]+)\'\)\)(.*?)\?\>/i',$contentFile,$Matches);
    			foreach ($Matches[2] as $key => $Match)
    			{
    				$contentFile = str_replace($Matches[0][$key],php_strip_whitespace(erLhcoreClassDesign::designtpl($Match)),$contentFile);
    			}

    	        //Compile templates inclusions first level.
    	        $Matches = array();
    			preg_match_all('/<\?php(\s*)include\(erLhcoreClassDesign::designtpl\(\'([a-zA-Z0-9-\.-\/\_]+)\'\)\)(.*?)\?\>/i',$contentFile,$Matches);
    			foreach ($Matches[2] as $key => $Match)
    			{
    				$contentFile = str_replace($Matches[0][$key],php_strip_whitespace(erLhcoreClassDesign::designtpl($Match)),$contentFile);
    			}
	        }



			//Compile image css paths. Etc..
			$Matches = array();
			preg_match_all('/<\?php echo erLhcoreClassDesign::design\(\'([a-zA-Z0-9-\.-\/\_]+)\'\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $Match)
			{
				$contentFile = str_replace($Matches[0][$key],erLhcoreClassDesign::design($Match),$contentFile);
			}

			//Compile translations, pure translations
			$Matches = array();
			preg_match_all('/<\?php echo erTranslationClassLhTranslation::getInstance\(\)->getTranslation\(\'(.*?)\',(.*?)\'(.*?)\'\)(.*?)\?\>/i',$contentFile,$Matches);

			foreach ($Matches[1] as $key => $TranslateContent)
			{
				$contentFile = str_replace($Matches[0][$key],erTranslationClassLhTranslation::getInstance()->getTranslation($TranslateContent,$Matches[3][$key]),$contentFile);
			}

			//Translations used in logical conditions
			$Matches = array();
			preg_match_all('/erTranslationClassLhTranslation::getInstance\(\)->getTranslation\(\'(.*?)\',(.*?)\'(.*?)\'\)/i',$contentFile,$Matches);

			foreach ($Matches[1] as $key => $TranslateContent)
			{
				$contentFile = str_replace($Matches[0][$key],'\''.erTranslationClassLhTranslation::getInstance()->getTranslation($TranslateContent,$Matches[3][$key]).'\'',$contentFile);
			}

			// Compile url addresses
			$Matches = array();
			preg_match_all('/<\?php echo erLhcoreClassDesign::baseurl\((.*?)\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],erLhcoreClassDesign::baseurl(trim($UrlAddress,'\'')),$contentFile);
			}

			// Compile url direct addresses
			$Matches = array();
			preg_match_all('/<\?php echo erLhcoreClassDesign::baseurldirect\((.*?)\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],erLhcoreClassDesign::baseurldirect(trim($UrlAddress,'\'')),$contentFile);
			}

			// Compile url direct addresses
			$Matches = array();
			preg_match_all('/<\?php echo erLhcoreClassDesign::baseurlsite\(\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],erLhcoreClassDesign::baseurlsite(),$contentFile);
			}

			// Compile css url addresses
			$Matches = array();
			preg_match_all('/<\?php echo erLhcoreClassDesign::designCSS\((.*?)\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],erLhcoreClassDesign::designCSS(trim($UrlAddress,'\'')),$contentFile);
			}

			// Compile css url addresses
			$Matches = array();
			preg_match_all('/<\?php echo erLhcoreClassDesign::designJS\((.*?)\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],erLhcoreClassDesign::designJS(trim($UrlAddress,'\'')),$contentFile);
			}

			// Compile url addresses in logical operations
			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::baseurl\((.*?)\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::baseurl(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

			// Compile url addresses in logical operations
			$Matches = array();
			preg_match_all('/erLhcoreClassDesign::baseurldirect\((.*?)\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$contentFile = str_replace($Matches[0][$key],'\''.erLhcoreClassDesign::baseurldirect(trim($UrlAddress,'\'')).'\'',$contentFile);
			}

			// Compile config settings, direct output
			$Matches = array();
			preg_match_all('/<\?php echo erConfigClassLhConfig::getInstance\(\)->getSetting\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?),(\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $valueConfig = erConfigClassLhConfig::getInstance()->getSetting($Matches[2][$key],$Matches[5][$key]);
			    $valueReplace = '';

			    if (is_bool($valueConfig)){
			        $valueReplace = $valueConfig == false ? 'false' : 'true';
			    } elseif (is_integer($valueConfig)) {
			        $valueReplace = $valueConfig;
			    } elseif (is_array($valueConfig)) {
			        $valueReplace = var_export($valueConfig,true);
			    } else {
			        $valueReplace = $valueConfig;
			    }

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			// Compile config settings
			$Matches = array();
			preg_match_all('/erConfigClassLhConfig::getInstance\(\)->getSetting\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?),(\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $valueConfig = erConfigClassLhConfig::getInstance()->getSetting($Matches[2][$key],$Matches[5][$key]);
			    $valueReplace = '';

			    if (is_bool($valueConfig)){
			        $valueReplace = $valueConfig == false ? 'false' : 'true';
			    } elseif (is_integer($valueConfig)) {
			        $valueReplace = $valueConfig;
			    } elseif (is_array($valueConfig)) {
			        $valueReplace = var_export($valueConfig,true);
			    } else {
			        $valueReplace = '\''.$valueConfig.'\'';
			    }

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			// Compile override config settings, used in title, description override
			$Matches = array();
			preg_match_all('/<\?php echo erConfigClassLhConfig::getInstance\(\)->getOverrideValue\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?),(\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)(.*?)\?\>/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $valueConfig = erConfigClassLhConfig::getInstance()->getOverrideValue($Matches[2][$key],$Matches[5][$key]);
			    $valueReplace = '';

			    if (is_bool($valueConfig)){
			        $valueReplace = $valueConfig == false ? 'false' : 'true';
			    } elseif (is_integer($valueConfig)) {
			        $valueReplace = $valueConfig;
			    } elseif (is_array($valueConfig)) {
			        $valueReplace = var_export($valueConfig,true);
			    } else {
			        $valueReplace = $valueConfig;
			    }

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			// Compile override config settings
			$Matches = array();
			preg_match_all('/erConfigClassLhConfig::getInstance\(\)->getOverrideValue\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?),(\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)/i',$contentFile,$Matches);
			foreach ($Matches[1] as $key => $UrlAddress)
			{
				$valueConfig = erConfigClassLhConfig::getInstance()->getOverrideValue($Matches[2][$key],$Matches[5][$key]);
				$valueReplace = '';

				if (is_bool($valueConfig)){
					$valueReplace = $valueConfig == false ? 'false' : 'true';
				} elseif (is_integer($valueConfig)) {
					$valueReplace = $valueConfig;
				} elseif (is_array($valueConfig)) {
					$valueReplace = var_export($valueConfig,true);
				} else {
					$valueReplace = '\''.$valueConfig.'\'';
				}

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			$Matches = array();
			preg_match_all('/<\?php echo erConfigClassLhConfig::getInstance\(\)->getDirLanguage\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)(.*?)\?\>/i',$contentFile,$Matches);

			foreach ($Matches[1] as $key => $UrlAddress)
			{
			    $valueConfig = erConfigClassLhConfig::getInstance()->getDirLanguage($Matches[2][$key]);
			    $valueReplace = '';

			    if (is_bool($valueConfig)){
			        $valueReplace = $valueConfig == false ? 'false' : 'true';
			    } elseif (is_integer($valueConfig)) {
			        $valueReplace = $valueConfig;
			    } elseif (is_array($valueConfig)) {
			        $valueReplace = var_export($valueConfig,true);
			    } else {
			        $valueReplace = $valueConfig;
			    }

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			// Compile config settings
			$Matches = array();
			preg_match_all('/erConfigClassLhConfig::getInstance\(\)->getDirLanguage\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)/i',$contentFile,$Matches);



			foreach ($Matches[1] as $key => $var)
			{
				$valueConfig = erConfigClassLhConfig::getInstance()->getDirLanguage($Matches[2][$key]);
				$valueReplace = '';

				if (is_bool($valueConfig)){
					$valueReplace = $valueConfig == false ? 'false' : 'true';
				} elseif (is_integer($valueConfig)) {
					$valueReplace = $valueConfig;
				} elseif (is_array($valueConfig)) {
					$valueReplace = var_export($valueConfig,true);
				} else {
					$valueReplace = '\''.$valueConfig.'\'';
				}

				$contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
			}

			if ($this->cacheDbVariables == true) {		
					
				// Compile config completely
	            $Matches = array();
	            preg_match_all('/<\?php echo erLhcoreClassModelChatConfig::fetch\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->current_value(.*?)\?\>/i',$contentFile,$Matches);
	            foreach ($Matches[1] as $key => $UrlAddress)
	            {
	                $valueConfig = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->current_value;             
	                $contentFile = str_replace($Matches[0][$key],$valueConfig,$contentFile);
	            }			
            
				// Compile config settings in php scripts
	            $Matches = array();
	            preg_match_all('/erLhcoreClassModelChatConfig::fetch\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->current_value/i',$contentFile,$Matches);
	            foreach ($Matches[1] as $key => $UrlAddress)
	            {
	                $valueConfig = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->current_value;
	                $valueReplace = '';
	                $valueReplace = '\''.str_replace("'","\'",$valueConfig).'\'';
	                $contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
	            }
            	            
				// Compile config settings in php scripts
	            $Matches = array();
	            preg_match_all('/erLhcoreClassModelChatConfig::fetch\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->data_value/i',$contentFile,$Matches);
	            foreach ($Matches[1] as $key => $UrlAddress)
	            {
	                $valueConfig = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->data_value;
	                $valueReplace = var_export($valueConfig,true);
	                $contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
	            }            
	            
				// Compile config settings array
	            $Matches = array();
	            preg_match_all('/erLhcoreClassModelChatConfig::fetch\((\s?)\'([a-zA-Z0-9-\.-\/\_]+)\'(\s?)\)->data\[\'([a-zA-Z0-9-\.-\/\_]+)\'\]/i',$contentFile,$Matches);           
	            foreach ($Matches[1] as $key => $UrlAddress)
	            {
	            	
	                $valueConfig = erLhcoreClassModelChatConfig::fetch($Matches[2][$key])->data[$Matches[4][$key]];
	                $valueReplace = '';
	                $valueReplace = '\''.str_replace("'","\'",$valueConfig).'\'';
	                $contentFile = str_replace($Matches[0][$key],$valueReplace,$contentFile);
	            }
			}
            
			// Compile content language
			$contentFile = str_replace('<?php echo erLhcoreClassSystem::instance()->ContentLanguage?>',erLhcoreClassSystem::instance()->ContentLanguage,$contentFile);

			// Compile siteaccess
			$contentFile = str_replace('erLhcoreClassSystem::instance()->SiteAccess','\''.erLhcoreClassSystem::instance()->SiteAccess.'\'',$contentFile);


			// Atomoc template compilation to avoid concurent request compiling and writing to the same file
			$fileName = 'cache/compiledtemplates/'.md5(time().rand(0, 1000).microtime().$file.$instance->WWWDirLang.$instance->Language.$port).'.php';
			file_put_contents($fileName,erLhcoreClassTemplate::strip_html($contentFile));

			$file = 'cache/compiledtemplates/'.md5($file.$instance->WWWDirLang.$instance->Language.$port).'.php';
			rename($fileName,$file);

	 	    $this->cacheTemplates[md5($fileTemplate.$instance->WWWDirLang.$instance->Language.$port)] = $file;
			$this->storeCache();
        }

		return $this->fetchExecute($file);

    }

	function storeCache()
	{
	    if (is_null($this->cacheWriter)) {
            $this->cacheWriter = new erLhcoreClassCacheStorage( 'cache/cacheconfig/');
	    }

		try {
			$this->cacheWriter->store('templateCache',$this->cacheTemplates);
		} catch (Exception $e) {
			// Do nothing, this happens on a lot of requests
		}

		$cacheObj = CSCacheAPC::getMem();
		$cacheObj->store('templateCacheArray_version_'.$cacheObj->getCacheVersion('site_version'),$this->cacheTemplates);
	}


	function fetchExecute($file)
	{
		@extract($this->vars,EXTR_REFS);        // Extract the vars to local namespace
        ob_start();                             // Start output buffering
        $result = include($file);               // Include the file
        if ($result === false) {                 // Make sure file was included succesfuly
            throw new Exception("File inclusion failed"); // Throw exception if failed, so tpl compiler will recompile template
        }
        $contents = ob_get_contents(); // Get the contents of the buffer
        ob_end_clean();                // End buffering and discard
        return $contents;
	}

}


?>