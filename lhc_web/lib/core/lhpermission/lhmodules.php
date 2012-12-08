<?php

class erLhcoreClassModules{
      
   function __construct()
   {
 
   }
   
   public static function getModuleList()
   {       
        $ModulesDir = 'modules';
        
        $ModuleList = array();
        
        $Modules = ezcBaseFile::findRecursive( $ModulesDir,array( '@module.php@' ) );
      
        foreach ($Modules as $ModuleInclude)
        {
            include($ModuleInclude); 
            $ModuleList[str_replace('modules/','',dirname($ModuleInclude))] = array('name' => $Module['name']);
        }
                       
        return $ModuleList ;
   }

   public static function getModuleFunctions($ModulePath)
   {
       include('modules/' . $ModulePath . '/module.php');
       return $FunctionList;
   }

}


?>