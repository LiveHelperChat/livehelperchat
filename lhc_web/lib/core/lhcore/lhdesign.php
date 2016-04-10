<?php

class erLhcoreClassDesign
{

    private static $disabledTheme = array();

    public static function appendDisabledTheme($themeDisable)
    {
        self::$disabledTheme[] = $themeDisable;
    }
    
    public static function design($path)
    {
        $debugOutput = erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output');
        
        if ($debugOutput == true) {
            $logString = '';
            $debug = ezcDebug::getInstance();
        }
        
        $instance = erLhcoreClassSystem::instance();
        
        // Check extensions directories
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'extensions');
        foreach ($extensions as $ext) {
            $tplDir = $instance->SiteDir . 'extension/' . $ext . '/design/' . $ext . 'theme/' . $path;
            if (file_exists($tplDir)) {
                if ($debugOutput == true) {
                    $logString .= "Found IN - " . $tplDir . "<br/>";
                    $debug->log($logString, 0, array(
                        "source" => "erLhcoreClassDesign",
                        "category" => "designtpl - $path"
                    ));
                }
                
                return $instance->wwwDir() . '/extension/' . $ext . '/design/' . $ext . 'theme/' . $path;
            } else {
                if ($debugOutput == true)
                    $logString .= "Not found IN - " . $tplDir . "<br/>";
            }
        }
        
        foreach ($instance->ThemeSite as $designDirectory) {
            if (! in_array($designDirectory, self::$disabledTheme)) {
                
                $fileDir = $instance->SiteDir . 'design/' . $designDirectory . '/' . $path;
                
                if (file_exists($fileDir)) {
                    
                    if ($debugOutput == true) {
                        $logString .= "Found IN - " . $fileDir . "<br/>";
                        $debug->log($logString, 0, array(
                            "source" => "erLhcoreClassDesign",
                            "category" => "design - $path"
                        ));
                    }
                    
                    return $instance->wwwDir() . '/design/' . $designDirectory . '/' . $path;
                } else {
                    if ($debugOutput == true)
                        $logString .= "Not found IN - " . $fileDir . "<br/>";
                }
            }
        }
        
        if ($debugOutput == true)
            $debug->log($logString, 0, array(
                "source" => "shop",
                "erLhcoreClassDesign" => "design - $path"
            ));
    }

    public static function designtpl($path)
    {
        $debugOutput = erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output');
        
        if ($debugOutput == true) {
            $logString = '';
            $debug = ezcDebug::getInstance();
        }
        
        $instance = erLhcoreClassSystem::instance();
        
        $isMultiInclude = strpos($path, '_multiinclude') !== false;
        $multiTemplates = array();
        
        // Check extensions directories
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'extensions');
        foreach ($extensions as $ext) {
            $tplDir = $instance->SiteDir . 'extension/' . $ext . '/design/' . $ext . 'theme/tpl/' . $path;
            if (file_exists($tplDir)) {
                if ($debugOutput == true) {
                    $logString .= "Found IN - " . $tplDir . "<br/>";
                }
                
                if ($isMultiInclude === false) {
                    if ($debugOutput == true) {
                        $debug->log($logString, 0, array(
                            "source" => "erLhcoreClassDesign",
                            "category" => "designtpl - $path"
                        ));
                    }
                    return $tplDir;
                } else {
                    $multiTemplates[] = $tplDir;
                }
            } else {
                if ($debugOutput == true)
                    $logString .= "Not found IN - " . $tplDir . "<br/>";
            }
        }
        
        // Check default themes
        foreach ($instance->ThemeSite as $designDirectory) {
            if (! in_array($designDirectory, self::$disabledTheme)) {
                $tplDir = $instance->SiteDir . 'design/' . $designDirectory . '/tpl/' . $path;
                
                if (file_exists($tplDir)) {
                    
                    if ($debugOutput == true) {
                        $logString .= "Found IN - " . $tplDir . "<br/>";
                    }
                    
                    if ($isMultiInclude === false) {
                        if ($debugOutput == true) {
                            $debug->log($logString, 0, array(
                                "source" => "erLhcoreClassDesign",
                                "category" => "designtpl - $path"
                            ));
                        }
                        return $tplDir;
                    } else {
                        $multiTemplates[] = $tplDir;
                    }
                } else {
                    if ($debugOutput == true)
                        $logString .= "Not found IN - " . $tplDir . "<br/>";
                }
            }
        }
        
        if ($isMultiInclude === true && ! empty($multiTemplates)) {
            
            // Only one template was found, return it instantly
            if (count($multiTemplates) == 1) {
                if ($debugOutput == true)
                    $debug->log($logString, 0, array(
                        "source" => "shop",
                        "erLhcoreClassDesign" => "designtpl - $path"
                    ));
                return array_shift($multiTemplates);
            }
            
            $multiTemplates = array_reverse($multiTemplates);
            $contentFile = '';
            foreach ($multiTemplates as $template) {
                $contentFile .= php_strip_whitespace($template);
            }
            
            // Atomic template compilation to avoid concurent request compiling and writing to the same file
            $fileName = 'cache/compiledtemplates/' . md5(time() . rand(0, 1000) . microtime() . $path . $instance->WWWDirLang . $instance->Language) . '.php';
            file_put_contents($fileName, $contentFile);
            
            $file = 'cache/compiledtemplates/' . md5('multi_' . $path . $instance->WWWDirLang . $instance->Language) . '.php';
            rename($fileName, $file);
            
            if ($debugOutput == true) {
                $logString .= "COMPILED MULTITEMPLATE - " . $file . "<br/>";
                $debug->log($logString, 0, array(
                    "source" => "shop",
                    "erLhcoreClassDesign" => "designtpl - $path"
                ));
            }
            
            return $file;
        }
        
        if ($debugOutput == true)
            $debug->log($logString, 0, array(
                "source" => "shop",
                "erLhcoreClassDesign" => "designtpl - $path"
            ));
        
        return;
    }

    public static function imagePath($path, $useCDN = false, $id = 0)
    {
        $instance = erLhcoreClassSystem::instance();
        if ($useCDN == false) {
            return erConfigClassLhConfig::getInstance()->getSetting('cdn', 'full_img_cdn') . $instance->wwwDir() . '/albums/' . $path;
        } else {
            $cfg = erConfigClassLhConfig::getInstance();
            $cdnServers = $cfg->getSetting('cdn', 'images');
            return $cdnServers[$id % count($cdnServers)] . $instance->wwwDir() . '/albums/' . $path;
        }
    }

    public static function baseurlRerun($link = '')
    {
        $instance = erLhcoreClassSystem::instance();
        $link = ltrim($link, '/');
        return $instance->WWWDirLang . '/' . $link;
    }

    public static function baseurl($link = '')
    {
        $instance = erLhcoreClassSystem::instance();
        $link = ltrim($link, '/');
        return $instance->WWWDir . $instance->IndexFile . $instance->WWWDirLang . '/' . $link;
    }

    public static function baseurldirect($link = '')
    {
        $instance = erLhcoreClassSystem::instance();
        return $instance->WWWDir . $instance->IndexFile . '/' . ltrim($link, '/');
    }

    public static function baseurlsite()
    {
        $instance = erLhcoreClassSystem::instance();
        return $instance->WWWDir . $instance->IndexFile;
    }

    public static function designCSS($files)
    {
        $debugOutput = erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output');
        $items = explode(';', $files);
        
        if ($debugOutput == true) {
            $logString = '';
            $debug = ezcDebug::getInstance();
        }
        
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'extensions');
        $instance = erLhcoreClassSystem::instance();
        
        $filesToCompress = '';
        foreach ($items as $path) {
            $fileFound = false;
            
            /**
             * Check extension folders first
             */
            foreach ($extensions as $ext) {
                $fileDir = $instance->SiteDir . '/extension/' . $ext . '/design/' . $ext . 'theme/' . $path;
                if (file_exists($fileDir)) {
                    
                    $fileContent = file_get_contents($fileDir);
                    
                    if (preg_match_all("/url\(\s*[\'|\"]?([A-Za-z0-9_\-\/\.\\%?&#]+)[\'|\"]?\s*\)/ix", $fileContent, $urlMatches)) {
                        $urlMatches = array_unique($urlMatches[1]);
                        foreach ($urlMatches as $match) {
                            $match = str_replace('\\', '/', $match);
                            // Replace path if it is realtive
                            if ($match[0] !== '/' and strpos($match, 'http:') === false) {
                                $appendMatch = '';
                                $matchOriginal = $match;
                                
                                if (strpos($match, '?') !== false) {
                                    $matchParts = explode('?', $match);
                                    $match = $matchParts[0];
                                    $appendMatch = '?' . $matchParts[1];
                                }
                                
                                $newMatchPath = self::design(str_replace('../', '', $match)) . $appendMatch;
                                $fileContent = str_replace($matchOriginal, $newMatchPath, $fileContent);
                            }
                        }
                    }
                    
                    $filesToCompress .= $fileContent;
                    $fileFound = true;
                    
                    if ($debugOutput == true) {
                        $logString .= "<b>Found IN - " . $fileDir . "</b><br/>";
                    }
                    
                    break;
                } else {
                    if ($debugOutput == true)
                        $logString .= "Not found IN - " . $fileDir . "<br/>";
                }
            }
            
            /**
             * If not found in any extension use standard search
             */
            if ($fileFound == false) {
                // Check themes directories
                foreach ($instance->ThemeSite as $designDirectory) {
                    if (! in_array($designDirectory, self::$disabledTheme)) {
                        
                        $fileDir = $instance->SiteDir . 'design/' . $designDirectory . '/' . $path;
                        
                        if (file_exists($fileDir)) {
                            
                            $fileContent = file_get_contents($fileDir);
                            
                            if (preg_match_all("/url\(\s*[\'|\"]?([A-Za-z0-9_\-\/\.\\%?&#]+)[\'|\"]?\s*\)/ix", $fileContent, $urlMatches)) {
                                $urlMatches = array_unique($urlMatches[1]);
                                
                                foreach ($urlMatches as $match) {
                                    $match = str_replace('\\', '/', $match);
                                    // Replace path if it is realtive
                                    if ($match[0] !== '/' and strpos($match, 'http:') === false) {
                                        $appendMatch = '';
                                        $matchOriginal = $match;
                                        
                                        if (strpos($match, '?') !== false) {
                                            $matchParts = explode('?', $match);
                                            $match = $matchParts[0];
                                            $appendMatch = '?' . $matchParts[1];
                                        }
                                        
                                        $newMatchPath = self::design(str_replace('../', '', $match)) . $appendMatch;
                                        $fileContent = str_replace($matchOriginal, $newMatchPath, $fileContent);
                                    }
                                }
                            }
                            
                            $filesToCompress .= $fileContent;
                            $fileFound = true;
                            
                            if ($debugOutput == true) {
                                $logString .= "<b>Found IN - " . $fileDir . "</b><br/>";
                            }
                            
                            break;
                        } else {
                            if ($debugOutput == true)
                                $logString .= "Not found IN - " . $fileDir . "<br/>";
                        }
                    }
                }
            }
        }
        
        $sys = erLhcoreClassSystem::instance()->SiteDir;
        $filesToCompress = self::optimizeCSS($filesToCompress, 3);
        $fileName = md5($filesToCompress . $instance->WWWDirLang . $instance->Language);
        $file = $sys . 'cache/compiledtemplates/' . $fileName . '.css';
        
        if (! file_exists($file)) {
            file_put_contents($file, $filesToCompress);
        }
        
        if ($debugOutput == true)
            $debug->log($logString, 0, array(
                "source" => "shop",
                "erLhcoreClassDesign" => "designCSS - $path"
            ));
        
        return $instance->wwwDir() . '/cache/compiledtemplates/' . $fileName . '.css';
    }

    public static function mb_wordwrap($str, $width = 75, $break = "\n", $cut = false)
    {
        $lines = explode($break, $str);
        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width)
                continue;
            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual . $word) <= $width)
                    $actual .= $word . ' ';
                else {
                    if ($actual != '')
                        $line .= rtrim($actual) . $break;
                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width) . $break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }
        return implode($break, $lines);
    }

    public static function shrt($string = '', $max = 10, $append = '...', $wordrap = 30, $encQuates = null)
    {
        $string = str_replace('&nbsp;', ' ', $string);
        $string = str_replace(array(
            '[b]',
            '[/b]',
            '[b]',
            '[/b]',
            '[ul]',
            '[/ul]',
            '[li]',
            '[/li]',
            '[i]',
            '[/i]'
        ), '', strip_tags($string));
        
        $string = self::mb_wordwrap($string, $wordrap, "\n", true);
        
        if (mb_strlen($string) <= $max)
            return htmlspecialchars($string, $encQuates);
        $cutted = mb_strcut($string, 0, $max, 'UTF-8') . $append;
        
        return htmlspecialchars($cutted, $encQuates);
    }

    public static function designJS($files)
    {
        $debugOutput = erConfigClassLhConfig::getInstance()->getSetting('site', 'debug_output');
        $items = explode(';', $files);
        
        if ($debugOutput == true) {
            $logString = '';
            $debug = ezcDebug::getInstance();
        }
        
        $filesToCompress = '';
        $instance = erLhcoreClassSystem::instance();
        $extensions = erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'extensions');
        
        foreach ($items as $path) {
            $fileFound = false;
            
            /**
             * Check extension folders first
             */
            foreach ($extensions as $ext) {
                $fileDir = $instance->SiteDir . '/extension/' . $ext . '/design/' . $ext . 'theme/' . $path;
                if (file_exists($fileDir)) {
                    
                    $fileContent = file_get_contents($fileDir);
                    
                    // normalize line feeds
                    $script = str_replace(array(
                        "\r\n",
                        "\r"
                    ), "\n", $fileContent);
                    
                    // remove multiline comments
                    $script = preg_replace('!(?:\n|\s|^)/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script);
                    $script = preg_replace('!(?:;)/\*[^*]*\*+([^/][^*]*\*+)*/!', ';', $script);
                    
                    // remove whitespace from start & end of line + singelline comment + multiple linefeeds
                    $script = preg_replace(array(
                        '/\n\s+/',
                        '/\s+\n/',
                        '#\n\s*//.*#',
                        '/\n+/'
                    ), "\n", $script);
                    
                    $filesToCompress .= $script . "\n";
                    
                    $fileFound = true;
                    
                    if ($debugOutput == true) {
                        $logString .= "<b>Found IN - " . $fileDir . "</b><br/>";
                    }
                    
                    break;
                } else {
                    if ($debugOutput == true)
                        $logString .= "Not found IN - " . $fileDir . "<br/>";
                }
            }
            
            if ($fileFound == false) {
                foreach ($instance->ThemeSite as $designDirectory) {
                    if (! in_array($designDirectory, self::$disabledTheme)) {
                        $fileDir = $instance->SiteDir . 'design/' . $designDirectory . '/' . $path;
                        
                        if (file_exists($fileDir)) {
                            
                            $fileContent = file_get_contents($fileDir);
                            
                            // normalize line feeds
                            $script = str_replace(array(
                                "\r\n",
                                "\r"
                            ), "\n", $fileContent);
                            
                            // remove multiline comments
                            $script = preg_replace('!(?:\n|\s|^)/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script);
                            $script = preg_replace('!(?:;)/\*[^*]*\*+([^/][^*]*\*+)*/!', ';', $script);
                            
                            // remove whitespace from start & end of line + singelline comment + multiple linefeeds
                            $script = preg_replace(array(
                                '/\n\s+/',
                                '/\s+\n/',
                                '#\n\s*//.*#',
                                '/\n+/'
                            ), "\n", $script);
                            
                            $filesToCompress .= $script . "\n";
                            
                            $fileFound = true;
                            
                            if ($debugOutput == true) {
                                $logString .= "<b>Found IN - " . $fileDir . "</b><br/>";
                            }
                            
                            break;
                        } else {
                            if ($debugOutput == true)
                                $logString .= "Not found IN - " . $fileDir . "<br/>";
                        }
                    }
                }
            }
        }
        
        $sys = erLhcoreClassSystem::instance()->SiteDir;
        $fileName = md5($filesToCompress . $instance->WWWDirLang . $instance->Language);
        $file = $sys . 'cache/compiledtemplates/' . $fileName . '.js';
        
        if (! file_exists($file)) {
            file_put_contents($file, $filesToCompress);
        }
        
        if ($debugOutput == true)
            $debug->log($logString, 0, array(
                "source" => "shop",
                "erLhcoreClassDesign" => "designJS - $path"
            ));
        
        return $instance->wwwDir() . '/cache/compiledtemplates/' . $fileName . '.js';
    }

    /**
     * 'compress' css code by removing whitespace
     *
     * @param string $css
     *            Concated Css string
     * @param int $packLevel
     *            Level of packing, values: 2-3
     * @return string
     */
    static function optimizeCSS($css, $packLevel)
    {
        // normalize line feeds
        $css = str_replace(array(
            "\r\n",
            "\r"
        ), "\n", $css);
        
        // remove multiline comments
        $css = preg_replace('!(?:\n|\s|^)/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = preg_replace('!(?:;)/\*[^*]*\*+([^/][^*]*\*+)*/!', ';', $css);
        
        // remove whitespace from start and end of line + multiple linefeeds
        $css = preg_replace(array(
            '/\n\s+/',
            '/\s+\n/',
            '/\n+/'
        ), "\n", $css);
        
        if ($packLevel > 2) {
            // remove space around ':' and ','
            $css = preg_replace(array(
                '/:\s+/',
                '/\s+:/'
            ), ':', $css);
            $css = preg_replace(array(
                '/,\s+/',
                '/\s+,/'
            ), ',', $css);
            
            // remove unnecesery line breaks
            $css = str_replace(array(
                ";\n",
                '; '
            ), ';', $css);
            $css = str_replace(array(
                "}\n",
                "\n}",
                ';}'
            ), '}', $css);
            $css = str_replace(array(
                "{\n",
                "\n{",
                '{;'
            ), '{', $css);
            
            // optimize css
            $css = str_replace(array(
                ' 0em',
                ' 0px',
                ' 0pt',
                ' 0pc'
            ), ' 0', $css);
            $css = str_replace(array(
                ':0em',
                ':0px',
                ':0pt',
                ':0pc'
            ), ':0', $css);
            $css = str_replace(' 0 0 0 0;', ' 0;', $css);
            $css = str_replace(':0 0 0 0;', ':0;', $css);
            
            // these should use regex to work on all colors
            $css = str_replace(array(
                '#ffffff',
                '#FFFFFF'
            ), '#fff', $css);
            $css = str_replace('#000000', '#000', $css);
        }
        return $css;
    }

    private static $moduleTranslations = null;
}

?>