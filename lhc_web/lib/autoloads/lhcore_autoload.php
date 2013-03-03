<?php


return array_merge(array (
        'erLhcoreClassModule'   => 'lib/core/lhcore/lhmodule.php',
        'erLhcoreClassSystem'   => 'lib/core/lhcore/lhsys.php',
        'erLhcoreClassDesign'   => 'lib/core/lhcore/lhdesign.php',        
        'erLhcoreClassTemplate' => 'lib/core/lhtpl/tpl.php',
        'erLhcoreClassURL'      => 'lib/core/lhcore/lhurl.php',        
        'erConfigClassLhConfig' => 'lib/core/lhconfig/lhconfig.php',                
        'lhPaginator'           => 'lib/core/lhexternal/lhpagination.php', 
        'erLhcoreClassLog'      => 'lib/core/lhcore/lhlog.php', 
        'erLhcoreClassLazyDatabaseConfiguration' => 'lib/core/lhcore/lhdb.php',          
             
        'erConfigClassLhCacheConfig' => 'lib/core/lhconfig/lhcacheconfig.php',   
             
        'erLhcoreClassTransfer' => 'lib/core/lhcore/lhtransfer.php',
        'PHPMailer'             => 'lib/core/lhcore/class.phpmailer.php',
                
        'erLhcoreClassRole'     => 'lib/core/lhpermission/lhrole.php',
        'erLhcoreClassModules'  => 'lib/core/lhpermission/lhmodules.php',
        'erLhcoreClassRoleFunction'  => 'lib/core/lhpermission/lhrolefunction.php',
        'erLhcoreClassGroupRole'  => 'lib/core/lhpermission/lhgrouprole.php',
        'erLhcoreClassGroupUser'  => 'lib/core/lhuser/lhgroupuser.php',
                 
        // Translations
        'erTranslationClassLhTranslation' => 'lib/core/lhcore/lhtranslation.php',
        
        // Core clases models
        'erLhcoreClassUser'         => 'lib/core/lhuser/lhuser.php',
        'erLhcoreClassGroup'        => 'lib/core/lhuser/lhgroup.php',
        'erLhcoreClassChat'         => 'lib/core/lhchat/lhchat.php',
        'erLhcoreClassChatValidator'=> 'lib/core/lhchat/lhchatvalidator.php',
        
        
        'erLhcoreClassDepartament' => 'lib/core/lhdepartament/lhdepartament.php',
        'erLhcoreClassUserDep'      => 'lib/core/lhdepartament/lhuserdep.php',
        
        'erLhcoreClassRenderHelper' => 'lib/core/lhcore/lhrenderhelper.php',
        
         // Models
        'erLhcoreClassModelTransfer' => 'lib/models/lhtransfer/erlhcoreclassmodeltransfer.php',
        
        
        'erLhcoreClassModelUser'            => 'lib/models/lhuser/erlhcoreclassmodeluser.php',
        'erLhcoreClassModelGroup'           => 'lib/models/lhuser/erlhcoreclassmodelgroup.php',
        'erLhcoreClassModelGroupUser'       => 'lib/models/lhuser/erlhcoreclassmodelgroupuser.php',
        'erLhcoreClassModelForgotPassword'  => 'lib/models/lhuser/erlhcoreclassmodelforgotpassword.php',
        
        'erLhcoreClassModelGroupRole'   => 'lib/models/lhpermission/erlhcoreclassmodelgrouprole.php',
        'erLhcoreClassModelChat'        => 'lib/models/lhchat/erlhcoreclassmodelchat.php',
        'erLhcoreClassModelmsg'         => 'lib/models/lhchat/erlhcoreclassmodelmsg.php',
        'erLhcoreClassModelCannedMsg'   => 'lib/models/lhchat/erlhcoreclassmodelcannedmsg.php',
        'erLhcoreClassModelChatConfig'  => 'lib/models/lhchat/erlhcoreclassmodelchatconfig.php',
        
        'erLhcoreClassModelChatOnlineUser'   => 'lib/models/lhchat/erlhcoreclassmodelchatonlineuser.php',
        'erLhcoreClassModelChatBlockedUser'  => 'lib/models/lhchat/erlhcoreclassmodelchatblockeduser.php',
        'erLhcoreClassModelRole'        => 'lib/models/lhpermission/erlhcoreclassmodelrole.php',
        'erLhcoreClassModelRoleFunction'=> 'lib/models/lhpermission/erlhcoreclassmodelrolefunction.php',
        'erLhcoreClassModelDepartament' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartament.php',
),
include('var/autoloads/lhextension_autoload.php')
);
    
?>