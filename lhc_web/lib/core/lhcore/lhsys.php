<?php

class erLhcoreClassSystem{
    
    
    static function instance()
    {
        if ( empty( $GLOBALS['LhSysInstance'] ) )
        {
            $GLOBALS['LhSysInstance'] = new erLhcoreClassSystem();
        }
        return $GLOBALS['LhSysInstance'];
    }
    
    
    static function init()
    {
		$index = "index.php";
		$def_index = '';
       
        $instance = erLhcoreClassSystem::instance();
       
 		$isCGI = ( substr( php_sapi_name(), 0, 3 ) == 'cgi' );
        $force_VirtualHost = false;        

        $phpSelf = $_SERVER['PHP_SELF'];

        // Find out, where our files are.
        if ( preg_match( "!(.*/)$index$!", $_SERVER['SCRIPT_FILENAME'], $regs ) )
        {
            $siteDir = $regs[1];
            $index = "/$index";
        }
        elseif ( preg_match( "!(.*/)$index/?!", $phpSelf, $regs ) )
        {
            // Some people using CGI have their $_SERVER['SCRIPT_FILENAME'] not right... so we are trying this.
            $siteDir = $_SERVER['DOCUMENT_ROOT'] . $regs[1];
            $index = "/$index";
        }
        else
        {
            // Fallback... doesn't work with virtual-hosts, but better than nothing
            $siteDir = "./";
            $index = "/$index";
        }
        if ( $isCGI and !$force_VirtualHost )
        {
            $index .= '?';
        }

        $scriptName = $_SERVER['SCRIPT_NAME'];
        // Get the webdir.

        $wwwDir = "";

        if ( $force_VirtualHost )
        {
            $wwwDir = "";
        }
        else
        {
            if ( preg_match( "!(.*)$index$!", $scriptName, $regs ) )
                $wwwDir = $regs[1];
            if ( preg_match( "!(.*)$index$!", $phpSelf, $regs ) )
                $wwwDir = $regs[1];
        }

        if ( ! $isCGI || $force_VirtualHost )
        {
            $requestURI = $_SERVER['REQUEST_URI'];
        }
        else
        {
            $requestURI = $_SERVER['QUERY_STRING'];

            /* take out PHPSESSID, if url-encoded */
            if ( preg_match( "/(.*)&PHPSESSID=[^&]+(.*)/", $requestURI, $matches ) )
            {
                $requestURI = $matches[1].$matches[2];
            }
        }

        // Fallback... Finding the paths above failed, so $_SERVER['PHP_SELF'] is not set right.
        if ( $siteDir == "./" )
            $phpSelf = $requestURI;

        if ( ! $isCGI )
        {
            $index_reg = str_replace( ".", "\\.", $index );
            // Trick: Rewrite setup doesn't have index.php in $_SERVER['PHP_SELF'], so we don't want an $index
            if ( !preg_match( "!.*$index_reg.*!", $phpSelf ) || $force_VirtualHost )
            {
                $index = "";
            }
            else
            {                
                // Get the right $_SERVER['REQUEST_URI'], when using nVH setup.
                if ( preg_match( "!^$wwwDir$index(.*)!", $phpSelf, $req ) )
                {
                    if ( !$req[1] )
                    {
                        if ( $phpSelf != "$wwwDir$index" and preg_match( "!^$wwwDir(.*)!", $requestURI, $req ) )
                        {
                            $requestURI = $req[1];
                            $index = '';
                        }
                        elseif ( $phpSelf == "$wwwDir$index" and
                               ( preg_match( "!^$wwwDir$index(.*)!", $requestURI, $req ) or preg_match( "!^$wwwDir(.*)!", $requestURI, $req ) ) )
                        {
                            $requestURI = $req[1];
                        }
                    }
                    else
                    {
                        $requestURI = $req[1];
                    }
                }
            }
        }
        if ( $isCGI and $force_VirtualHost )
            $index = '';
        // Remove url parameters
        if ( $isCGI and !$force_VirtualHost )
        {
            $pattern = "!(\/[^&]+)!";
        }
        else
        {
            $pattern = "!([^?]+)!";
        }
        if ( preg_match( $pattern, $requestURI, $regs ) )
        {
            $requestURI = $regs[1];
        }

        // Remove internal links
        if ( preg_match( "!([^#]+)!", $requestURI, $regs ) )
        {
            $requestURI = $regs[1];
        }

        if ( !$isCGI )
        {
            $currentPath = substr( $_SERVER['SCRIPT_FILENAME'] , 0, -strlen( 'index.php' ) );
            if ( strpos( $currentPath, $_SERVER['DOCUMENT_ROOT']  ) === 0 )
            {
                $prependRequest = substr( $currentPath, strlen( $_SERVER['DOCUMENT_ROOT'] ) );

                if ( strpos( $requestURI, $prependRequest ) === 0 )
                {
                    $requestURI = substr( $requestURI, strlen( $prependRequest ) - 1 );
                    $wwwDir = substr( $prependRequest, 0, -1 );
                }
            }
        }

    
        $instance->SiteDir = $siteDir;
        $instance->WWWDir = $wwwDir;
        $instance->WWWDirLang = '';
        $instance->IndexFile = $index;
        $instance->RequestURI = $requestURI;        
        
    }

    function wwwDir()
    {
        return $this->WWWDir;
    }
    
    /// The path to where all the code resides
    public $SiteDir;
    /// The access path of the current site view
    /// The relative directory path of the vhless setup
    public $WWWDir;
    
    // The www dir used in links formating
    public $WWWDirLang;
    
    /// The filepath for the index
    public $IndexFile;
    /// The uri which is used for parsing module/view information from, may differ from $_SERVER['REQUEST_URI']
    public $RequestURI;
    /// The type of filesystem, is either win32 or unix. This often used to determine os specific paths.
    
    /// Current language
    public $Language;
    public $LanguageShortname;

}


?>