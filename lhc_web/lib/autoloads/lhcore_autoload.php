<?php


return array_merge(array (
        'erLhcoreClassModule'   => 'lib/core/lhcore/lhmodule.php',
        'erLhcoreClassSystem'   => 'lib/core/lhcore/lhsys.php',
        'erLhcoreClassDesign'   => 'lib/core/lhcore/lhdesign.php',
        'erLhcoreClassTemplate' 	=> 'lib/core/lhtpl/tpl.php',
        'erLhcoreClassCacheStorage' => 'lib/core/lhtpl/tpl.php',
        'erLhcoreClassURL'      => 'lib/core/lhcore/lhurl.php',
        'erConfigClassLhConfig' => 'lib/core/lhconfig/lhconfig.php',
        'lhPaginator'           => 'lib/core/lhexternal/lhpagination.php',
        'erLhcoreClassLog'      => 'lib/core/lhcore/lhlog.php',
        'erLhcoreClassLazyDatabaseConfiguration' => 'lib/core/lhcore/lhdb.php',

        'erConfigClassLhCacheConfig' => 'lib/core/lhconfig/lhcacheconfig.php',

        'erLhcoreClassTransfer' => 'lib/core/lhcore/lhtransfer.php',
        'PHPMailer'             => 'lib/core/lhcore/class.phpmailer.php',
		'ezcAuthenticationDatabaseCredentialFilter' => 'lib/core/lhuser/lhauthenticationdatabasecredentialfilter.php',

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
		'erLhcoreClassChatWorkflow' => 'lib/core/lhchat/lhchatworkflow.php',
        'erLhcoreClassChatValidator'=> 'lib/core/lhchat/lhchatvalidator.php',


        'erLhcoreClassBBCode'       => 'lib/core/lhbbcode/lhbbcode.php',


        'erLhcoreClassDepartament' => 'lib/core/lhdepartament/lhdepartament.php',
        'erLhcoreClassUserDep'      => 'lib/core/lhdepartament/lhuserdep.php',

        'erLhcoreClassRenderHelper' => 'lib/core/lhcore/lhrenderhelper.php',

         // Models
        'erLhcoreClassModelTransfer' => 'lib/models/lhtransfer/erlhcoreclassmodeltransfer.php',


        'erLhcoreClassModelUser'            => 'lib/models/lhuser/erlhcoreclassmodeluser.php',
		'erLhcoreClassModelUserRemember' 	=> 'lib/models/lhuser/erlhcoreclassmodeluserremember.php',
        'erLhcoreClassModelGroup'           => 'lib/models/lhuser/erlhcoreclassmodelgroup.php',
        'erLhcoreClassModelGroupUser'       => 'lib/models/lhuser/erlhcoreclassmodelgroupuser.php',
        'erLhcoreClassModelForgotPassword'  => 'lib/models/lhuser/erlhcoreclassmodelforgotpassword.php',

        'erLhcoreClassModelUserSetting'  	   => 'lib/models/lhuser/erlhcoreclassmodelusersetting.php',
        'erLhcoreClassModelUserSettingOption'  => 'lib/models/lhuser/erlhcoreclassmodelusersettingoption.php',

        'erLhcoreClassModelGroupRole'   => 'lib/models/lhpermission/erlhcoreclassmodelgrouprole.php',
        'erLhcoreClassModelChat'        => 'lib/models/lhchat/erlhcoreclassmodelchat.php',
        'erLhcoreClassModelmsg'         => 'lib/models/lhchat/erlhcoreclassmodelmsg.php',
        'erLhcoreClassModelCannedMsg'   => 'lib/models/lhchat/erlhcoreclassmodelcannedmsg.php',
        'erLhcoreClassModelChatConfig'  => 'lib/models/lhchat/erlhcoreclassmodelchatconfig.php',

        'erLhcoreClassModelChatOnlineUser'   		=> 'lib/models/lhchat/erlhcoreclassmodelchatonlineuser.php',
        'erLhcoreClassModelChatOnlineUserFootprint' => 'lib/models/lhchat/erlhcoreclassmodelchatonlineuserfootprint.php',
        'erLhcoreClassModelChatBlockedUser'  		=> 'lib/models/lhchat/erlhcoreclassmodelchatblockeduser.php',
        'erLhcoreClassModelRole'        => 'lib/models/lhpermission/erlhcoreclassmodelrole.php',
        'erLhcoreClassModelRoleFunction'=> 'lib/models/lhpermission/erlhcoreclassmodelrolefunction.php',
        'erLhcoreClassModelDepartament' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartament.php',
        'erLhcoreClassModelUserDep' 	=> 'lib/models/lhdepartament/erlhcoreclassmodeluserdep.php',

		// Questionary module
		'erLhcoreClassModelQuestion'			=> 'lib/models/lhquestionary/erlhcoreclassmodelquestion.php',
		'erLhcoreClassQuestionary'				=> 'lib/core/lhquestionary/lhquestionary.php',
		'erLhcoreClassModelQuestionAnswer'		=> 'lib/models/lhquestionary/erlhcoreclassmodelquestionanswer.php',
		'erLhcoreClassModelQuestionOption'		=> 'lib/models/lhquestionary/erlhcoreclassmodelquestionoption.php',
		'erLhcoreClassModelQuestionOptionAnswer'=> 'lib/models/lhquestionary/erlhcoreclassmodelquestionoptionanswer.php',

		// FAQ
		'erLhcoreClassFaq' 			=> 'lib/core/lhfaq/lhfaq.php',
		'erLhcoreClassModelFaq' 	=> 'lib/models/lhfaq/erlhcoreclassmodelfaq.php',

		// Abstract module
		'erLhAbstractModelEmailTemplate' 			=> 'lib/models/lhabstract/erlhabstractmodelemailtemplate.php',
		'erLhAbstractModelProactiveChatInvitation'  => 'lib/models/lhabstract/erlhabstractmodeleproactivechatinvitation.php',
		'erLhAbstractModelAutoResponder'  			=> 'lib/models/lhabstract/erlhabstractmodelautoresponder.php',
		'erLhcoreClassAbstract' 		 => 'lib/core/lhabstract/lhabstract.php',
		'erLhcoreClassChatMail' 		 => 'lib/core/lhchat/lhchatmail.php',

		// Chatbox
		'erLhcoreClassChatbox'				=> 'lib/core/lhchatbox/lhchatbox.php',
		'erLhcoreClassModelChatbox'			=> 'lib/models/lhchatbox/erlhcoreclassmodelchatbox.php',

		 // Siteaccess generator
		'erLhcoreClassSiteaccessGenerator'	=> 'lib/core/lhcore/lhsiteaccessgenerator.php',

		 // Statistic
		'erLhcoreClassChatStatistic'		=> 'lib/core/lhchat/lhchatstatistic.php',

		 // Chat export
		'erLhcoreClassChatExport'				=> 'lib/core/lhchat/lhchatexport.php',

		// Chat archive
		'erLhcoreClassModelChatArchiveRange' 	=> 'lib/models/lhchat/erlhcoreclassmodelchatarchiverange.php',
		'erLhcoreClassModelChatArchive' 		=> 'lib/models/lhchat/erlhcoreclassmodelchatarchive.php',
		'erLhcoreClassModelChatArchiveMsg' 		=> 'lib/models/lhchat/erlhcoreclassmodelchatarchivemsg.php',

		// Files upload
		'UploadHandler' 						=> 'lib/core/lhcore/UploadHandler.php',
		'erLhcoreClassFileUpload' 				=> 'lib/core/lhcore/lhfileupload.php',
		'erLhcoreClassModelChatFile' 			=> 'lib/models/lhchat/erlhcoreclassmodelchatfile.php'


),
include('var/autoloads/lhextension_autoload.php')
);

?>