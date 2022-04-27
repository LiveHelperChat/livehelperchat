<?php

class erLhcoreClassModules {

   function __construct()
   {

   }

   public static function getModuleList()
   {
        $ModulesDir = 'modules';

        $ModuleList = array();
		$dirSeparator = DIRECTORY_SEPARATOR;


        $Modules = ezcBaseFile::findRecursive( $ModulesDir,array( '@module.php@' ) );

        foreach ($Modules as $ModuleInclude)
        {
            include($ModuleInclude);
            $ModuleList[str_replace("modules{$dirSeparator}",'',dirname($ModuleInclude))] = array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$Module['name']));
        }

        // Add extensions modules
        $cfg = erConfigClassLhConfig::getInstance();

        $extensions = $cfg->getSetting( 'site', 'extensions' );
        foreach ($extensions as $extension) {
        	if ( is_dir("extension/{$extension}/{$ModulesDir}") ) {
	        	$Modules = ezcBaseFile::findRecursive( "extension/{$extension}/{$ModulesDir}",array( '@module.php@' ) );
	        	foreach ($Modules as $ModuleInclude)
	        	{
	        		include($ModuleInclude);
	        		if (isset($ModuleList[str_replace("extension/{$extension}/{$ModulesDir}{$dirSeparator}",'',dirname($ModuleInclude))]['name'])){
	        			$ModuleList[str_replace("extension/{$extension}/{$ModulesDir}{$dirSeparator}",'',dirname($ModuleInclude))]['name'] .= ', EX - '.erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$Module['name']);
	        		} else {
	        			$ModuleList[str_replace("extension/{$extension}/{$ModulesDir}{$dirSeparator}",'',dirname($ModuleInclude))] = array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$Module['name']));
	        		}
	        	}
        	}
        }

        return $ModuleList ;
   }

   public static function getViewsByModule($ModulePath)
   {
       $ViewListProcessed = [];
       if (file_exists('modules/' . $ModulePath . '/module.php')) {
           include('modules/' . $ModulePath . '/module.php');
           if (isset($ViewList) && !empty($ViewList)) {
               $ViewListProcessed = array_merge($ViewListProcessed, array_keys($ViewList));
           }
       }
       $cfg = erConfigClassLhConfig::getInstance();
       // Append Override functions
       $extensions = $cfg->getSetting( 'site', 'extensions' );
       foreach ($extensions as $extension) {
           if (file_exists("extension/{$extension}/modules/{$ModulePath}/module.php")) {
               $ViewList = [];
               include("extension/{$extension}/modules/{$ModulePath}/module.php");
               if (isset($ViewList) && !empty($ViewList)) {
                   $ViewListProcessed = array_merge($ViewListProcessed, array_keys($ViewList));
               }
           }
       }

       return $ViewListProcessed;
   }

   public static function getModuleFunctions($ModulePath, $params = array())
   {
   		$FunctionListReturn = array();
   	    if (file_exists('modules/' . $ModulePath . '/module.php')) {
            $ViewList = [];
       		include('modules/' . $ModulePath . '/module.php');
       		$FunctionListReturn = $FunctionList;

           if (isset($ViewList) && !empty($ViewList) && isset($params['extract_url']) && $params['extract_url'] == true) {
               foreach ($ViewList as $urlKey => $viewItem) {
                   if (isset($viewItem['functions']) && !empty($viewItem['functions'])){
                       foreach ($viewItem['functions'] as $viewFunction) {
                           if (!isset($FunctionListReturn[$viewFunction]['url'])){
                               $FunctionListReturn[$viewFunction]['url'] = array();
                           }
                           $FunctionListReturn[$viewFunction]['url'][] = $ModulePath.'/'.$urlKey;
                       }
                   }
               }
           }
   	    }

   	    $cfg = erConfigClassLhConfig::getInstance();
   	    // Append Override functions
   	   	$extensions = $cfg->getSetting( 'site', 'extensions' );
   	   	foreach ($extensions as $extension) {
   	   		if (file_exists("extension/{$extension}/modules/{$ModulePath}/module.php")) {
                $ViewList = [];
   	   			include("extension/{$extension}/modules/{$ModulePath}/module.php");
   	   			if (isset($FunctionList)) {
   	   			  $FunctionListReturn = array_merge($FunctionListReturn,$FunctionList);
   	   			}
                if (isset($ViewList) && !empty($ViewList) && isset($params['extract_url']) && $params['extract_url'] == true) {
                    foreach ($ViewList as $urlKey => $viewItem) {
                        if (isset($viewItem['functions']) && !empty($viewItem['functions'])) {
                            foreach ($viewItem['functions'] as $viewFunction) {
                                if (!isset($FunctionListReturn[$viewFunction]['url'])){
                                    $FunctionListReturn[$viewFunction]['url'] = array();
                                }
                                $FunctionListReturn[$viewFunction]['url'][] = $ModulePath.'/'.$urlKey;
                            }
                        }
                    }
                }
   	   		}
   	   	}

   	   	foreach ($FunctionListReturn as & $Function) {
              if (isset($Function['explain'])){
                  $Function['explain'] = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$Function['explain']);
              }
   	   	}

   	   	return $FunctionListReturn;
   }

   public static function getModuleName($ModuleIdentifier) {
   	 	if ($ModuleIdentifier == '*'){
   	 		return erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','All modules');
   	 	}

   	 	$ModuleName = '';
   	 	if (file_exists('modules/' . $ModuleIdentifier . '/module.php')){
   	 		include('modules/' . $ModuleIdentifier . '/module.php');
   	 		$ModuleName = $Module['name'];
   	 	}

   	 	$cfg = erConfigClassLhConfig::getInstance();
   	 	$extensions = $cfg->getSetting( 'site', 'extensions' );
   	 	foreach ($extensions as $extension) {
   	 		if (file_exists("extension/{$extension}/modules/{$ModuleIdentifier}/module.php")) {
   	 			include("extension/{$extension}/modules/{$ModuleIdentifier}/module.php");
   	 			if (!empty($ModuleName)) {
   	 				$ModuleName .= ', EX - '.erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$Module['name']);
   	 			} else {
   	 				$ModuleName = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$Module['name']);
   	 			}
   	 		}
   	 	}

   	 	return $ModuleName;
   }

   public static function getFunctionName($ModuleIdentifier,$FunctionName) {

   	 	if ($FunctionName == '*') {
   	 		return erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','All functions');
   	 	}

   	 	$translatedFunctionName = '';

   	 	if (file_exists('modules/' . $ModuleIdentifier . '/module.php')){
   	 		include('modules/' . $ModuleIdentifier . '/module.php');
   	 		$translatedFunctionName = (isset($FunctionList[$FunctionName]['explain'])) ? erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$FunctionList[$FunctionName]['explain']) : '';
   	 	}

   	 	$cfg = erConfigClassLhConfig::getInstance();
   	 	$extensions = $cfg->getSetting( 'site', 'extensions' );
   	 	foreach ($extensions as $extension) {
   	 		if (file_exists("extension/{$extension}/modules/{$ModuleIdentifier}/module.php")) {
   	 			include("extension/{$extension}/modules/{$ModuleIdentifier}/module.php");
   	 			if (isset($FunctionList[$FunctionName]['explain'])){
	   	 			if (!empty($translatedFunctionName)) {
	   	 				$translatedFunctionName .= ', EX - '.erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$FunctionList[$FunctionName]['explain']);
	   	 			} else {
	   	 				$translatedFunctionName = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole',$FunctionList[$FunctionName]['explain']);
	   	 			}
   	 			}
   	 		}
   	 	}

   	 	return $translatedFunctionName;
   }

}


?>