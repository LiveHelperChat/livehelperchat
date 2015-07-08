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
        'erLhcoreClassIPDetect' => 'lib/core/lhcore/lhipdetect.php',

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
        'erLhcoreClassAdminChatValidatorHelper'=> 'lib/core/lhchat/lhchatadminvalidatorhelper.php',
        'erLhcoreClassChatCommand'=> 'lib/core/lhchat/lhchatcommand.php',
        'erLhcoreClassChatHelper' => 'lib/core/lhchat/lhchathelper.php',


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
		'erLhAbstractModelBrowseOfferInvitation'  	=> 'lib/models/lhabstract/erlhabstractmodelbrowseofferinvitation.php',
		'erLhAbstractModelForm'  					=> 'lib/models/lhabstract/erlhabstractmodelform.php',
		'erLhAbstractModelFormCollected'  			=> 'lib/models/lhabstract/erlhabstractmodelformcollected.php',
		'erLhAbstractModelWidgetTheme'  			=> 'lib/models/lhabstract/erlhabstractmodelwidgettheme.php',
		
		'erLhcoreClassFormRenderer'  		=> 'lib/core/lhform/lhformrenderer.php',
		'erLhcoreClassAbstract' 			=> 'lib/core/lhabstract/lhabstract.php',
		'erLhcoreClassChatMail' 		 	=> 'lib/core/lhchat/lhchatmail.php',

		// Chatbox
		'erLhcoreClassChatbox'				=> 'lib/core/lhchatbox/lhchatbox.php',
		'erLhcoreClassModelChatbox'			=> 'lib/models/lhchatbox/erlhcoreclassmodelchatbox.php',

		 // Siteaccess generator
		'erLhcoreClassSiteaccessGenerator'	=> 'lib/core/lhcore/lhsiteaccessgenerator.php',
		
		// Event dispatcher
		'erLhcoreClassChatEventDispatcher'	=> 'lib/core/lhchat/lhchateventdispatcher.php',

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
		'erLhcoreClassFileUploadAdmin' 			=> 'lib/core/lhcore/lhfileuploadadmin.php',
		'erLhcoreClassFileUpload' 				=> 'lib/core/lhcore/lhfileupload.php',
		'erLhcoreClassModelChatFile' 			=> 'lib/models/lhchat/erlhcoreclassmodelchatfile.php',


		'erLhcoreClassSearchHandler' 			=> 'lib/core/lhcore/lhsearchhandler.php',
		'erLhcoreClassInputForm' 				=> 'lib/core/lhcore/lhform.php',

		// Profile
		'erLhcoreClassImageConverter'               => 'lib/core/lhcore/lhimageconverter.php',
		'qqFileUploader'                            => 'lib/core/lhcore/lhimageconverter.php',
		'qqUploadedFileForm'                        => 'lib/core/lhcore/lhimageconverter.php',
		'qqUploadedFileXhr'                         => 'lib/core/lhcore/lhimageconverter.php',
		'erLhcoreClassGalleryImagemagickHandler'    => 'lib/core/lhcore/lhgalleryconverterhandler.php',
		'erLhcoreClassGalleryGDHandler'             => 'lib/core/lhcore/lhgallerygdconverterhandler.php',
		
		'XMPPHP_XMPP'								=> 'lib/core/lhxmp/XMPPHP/XMPP.php',
		'erLhcoreClassXMP'							=> 'lib/core/lhxmp/lhxmp.php',		
		'erLhcoreClassModelChatAccept'				=> 'lib/models/lhchat/erlhcoreclassmodelchataccept.php',
		
		'erLhcoreClassLhMemcache'                   => 'lib/core/lhcore/lhmemcache.php',	
		'erLhcoreClassLhRedis'                      => 'lib/core/lhcore/lhredis.php',
		'erLhcoreClassUpdate'                       => 'lib/core/lhcore/lhupdate.php',
		
		'GeoIp2\Database\Reader'					=> 'lib/core/lhexternal/GeoIp2/Database/Reader.php',		
		'GeoIp2\ProviderInterface'					=> 'lib/core/lhexternal/GeoIp2/ProviderInterface.php',	
		'GeoIp2\Model\Country'						=> 'lib/core/lhexternal/GeoIp2/Model/Country.php',	
		'GeoIp2\Model\City'							=> 'lib/core/lhexternal/GeoIp2/Model/City.php',	
		'GeoIp2\Record\Subdivision'					=> 'lib/core/lhexternal/GeoIp2/Record/Subdivision.php',	
		'GeoIp2\Record\Continent'					=> 'lib/core/lhexternal/GeoIp2/Record/Continent.php',	
		'GeoIp2\Record\AbstractPlaceRecord'			=> 'lib/core/lhexternal/GeoIp2/Record/AbstractPlaceRecord.php',	
		'GeoIp2\Record\AbstractRecord'				=> 'lib/core/lhexternal/GeoIp2/Record/AbstractRecord.php',	
		'GeoIp2\Record\Location'					=> 'lib/core/lhexternal/GeoIp2/Record/Location.php',	
		'GeoIp2\Record\Postal'						=> 'lib/core/lhexternal/GeoIp2/Record/Postal.php',	
		'GeoIp2\Record\Country'						=> 'lib/core/lhexternal/GeoIp2/Record/Country.php',	
		'GeoIp2\Record\City'						=> 'lib/core/lhexternal/GeoIp2/Record/City.php',	
		'GeoIp2\Record\MaxMind'						=> 'lib/core/lhexternal/GeoIp2/Record/MaxMind.php',	
		'GeoIp2\Record\RepresentedCountry'			=> 'lib/core/lhexternal/GeoIp2/Record/RepresentedCountry.php',	
		'GeoIp2\Record\Traits'						=> 'lib/core/lhexternal/GeoIp2/Record/Traits.php',	
		'GeoIp2\Exception\AddressNotFoundException' => 'lib/core/lhexternal/GeoIp2/Exception/AddressNotFoundException.php',	
		'GeoIp2\Exception\GeoIp2Exception' 			=> 'lib/core/lhexternal/GeoIp2/Exception/GeoIp2Exception.php',	
		'MaxMind\Db\Reader'							=> 'lib/core/lhexternal/MaxMind/Db/Reader.php',		
		'MaxMind\Db\Reader\Decoder'					=> 'lib/core/lhexternal/MaxMind/Db/Reader/Decoder.php',		
		'MaxMind\Db\Reader\Metadata'				=> 'lib/core/lhexternal/MaxMind/Db/Reader/Metadata.php',		
		'JsonSerializable'							=> 'lib/core/lhexternal/GeoIp2/Record/JsonSerializable.php',	
				
        // Speech
        'erLhcoreClassSpeech'                       => 'lib/core/lhspeech/lhspeech.php',
        'erLhcoreClassModelSpeechLanguage'          => 'lib/models/lhspeech/erlhcoreclassmodelspeechlanguage.php',
        'erLhcoreClassModelSpeechLanguageDialect'   => 'lib/models/lhspeech/erlhcoreclassmodelspeechlanguagedialect.php',
        'erLhcoreClassModelSpeechChatLanguage'      => 'lib/models/lhspeech/erlhcoreclassmodelspeechchatlanguage.php',
    
		// Co browse
		'erLhcoreClassCoBrowse' 					=> 'lib/core/lhcobrowse/lhcobrowse.php',
		'erLhcoreClassModelCoBrowse' 				=> 'lib/models/lhcobrowse/erlhcoreclassmodelcobrowse.php',
    
        // Translations
        'erLhcoreClassTranslate'                    => 'lib/core/lhtranslate/lhtranslate.php',
        'erLhcoreClassTranslateBing'                => 'lib/core/lhtranslate/lhbingtranslate.php',
        'erLhcoreClassTranslateGoogle'              => 'lib/core/lhtranslate/lhgoogletranslate.php',
		
),
include('var/autoloads/lhextension_autoload.php')
);

?>