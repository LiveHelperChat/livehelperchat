<?php

class erConfigClassLhConfig
{
    private static $instance = null;
    public $conf;

    public function __construct()
    {
	     $this->conf = @include('settings/settings.ini.php');
	  		     
         if ( !is_array($this->conf) ) {
		    	$this->conf = include('settings/settings.ini.default.php');
         }
    }

    public function getSetting($section, $key, $throwException = true)
    {
        if (isset($this->conf['settings'][$section][$key])) {
            return $this->conf['settings'][$section][$key];
        } else {
        	if ($throwException === true) {
            	throw new Exception('Setting with section {'.$section.'} value {'.$key.'}');
        	} else {
        		return false;
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

    /**
     * This function should be used then value can be override by siteAccess
     * or user language has influence to returned value
     *
     * */
    public function getDirLanguage($attribute = 'dir_language')
    {
        $value = null;

        if ($this->hasSetting('site',$attribute))
        $value = $this->getSetting('site',$attribute);

        $siteAccess = erLhcoreClassSystem::instance()->SiteAccess;
        
        if ($siteAccess == 'site_admin') {
	        $valueOverride = $this->getSetting('site_access_options',$siteAccess);
	
	        if (key_exists($attribute,$valueOverride)){
	        	// User has not changed default site access language. So just return current value.
	        	if (erLhcoreClassModelUserSetting::getSetting('user_language',$valueOverride['locale']) == $valueOverride['locale']){
	              	return $valueOverride[$attribute];
	        	} else { // User has changed default siteaccess language, we need to check does ltr or rtl matches
	        		foreach ($this->getSetting( 'site','available_site_access' ) as $siteaccess) { // Loop untill we find our locate siteaccess and check it's language direction
	        			$siteAccessOptions = $this->getSetting('site_access_options',$siteaccess);
	        			if ($siteAccessOptions['locale'] == erLhcoreClassModelUserSetting::getSetting('user_language',$valueOverride['locale'])){
	        				return $siteAccessOptions[$attribute];
	        			}
	        		}
	        	}
	        }
        } else {
        	$value = $this->getOverrideValue('site', $attribute);        	
        }
        
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