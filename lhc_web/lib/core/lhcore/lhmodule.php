<?php

class erLhcoreClassModule{
    
    static function runModule($Module,$Functions = array())
    {
        if (isset($Module[$GLOBALS['ViewToRun']]))
        {
            $Params = array();
            $Params['module'] = $Module[$GLOBALS['ViewToRun']];
            $Params['module']['name'] = $GLOBALS['ModuleToRun'];
            $Params['module']['view'] = $GLOBALS['ViewToRun'];
            
            if (isset($Module[$GLOBALS['ViewToRun']]['params']))
            {
                $urlCfgDefault = ezcUrlConfiguration::getInstance();
                $url = erLhcoreClassURL::getInstance();
                         
                foreach ($Module[$GLOBALS['ViewToRun']]['params'] as $userParameter)
                {           
                   $urlCfgDefault->addOrderedParameter( $userParameter );                                       
                   $url->applyConfiguration( $urlCfgDefault );

                   $Params['user_parameters'][$userParameter] =  $url->getParam($userParameter); 
                }
               
            }
            // Function set, check permission
            if (isset($Params['module']['functions']))
            {
                $currentUser = erLhcoreClassUser::instance();
                
                if (!$currentUser->isLogged()){
                    header('Location: '. erLhcoreClassSystem::instance()->WWWDir . '/index.php/user/login');
                    return ;
                }
                
                if (!$currentUser->hasAccessTo('lh'.$GLOBALS['ModuleToRun'],$Params['module']['functions']))
                {
                    include_once('modules/lhkernel/nopermission.php');  
                    return $Result;
                }
            }
            
            

            include_once('modules/lh'.$GLOBALS['ModuleToRun'].'/'.$GLOBALS['ViewToRun'].'.php');            
            return $Result;
        } else {
            echo 'Views not found -> ',$GLOBALS['ViewToRun'];
        }
    }
    
    static function redirect($url = '/')
    {        
        header('Location: '. erLhcoreClassSystem::instance()->WWWDir . '/index.php' . erLhcoreClassSystem::instance()->WWWDirLang . '/' .ltrim($url,'/') );
    }
}

?>