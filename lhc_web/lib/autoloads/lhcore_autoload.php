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
        'erLhcoreClassRestAPIHandler' => 'lib/core/lhrestapi/lhrestapivalidator.php',
        'erLhcoreClassRestAPIUserValidator' => 'lib/core/lhrestapi/lhrestapiuservalidator.php',

        'erLhcoreClassTransfer' => 'lib/core/lhcore/lhtransfer.php',
        'PHPMailer'             => 'lib/core/lhcore/class.phpmailer.php',
        'SMTP'                  => 'lib/core/lhcore/class.smtp.php',
		'ezcAuthenticationDatabaseCredentialFilter' => 'lib/core/lhuser/lhauthenticationdatabasecredentialfilter.php',

        'erLhcoreClassRole'     => 'lib/core/lhpermission/lhrole.php',
        'erLhcoreClassModules'  => 'lib/core/lhpermission/lhmodules.php',
        'erLhcoreClassRoleFunction'  => 'lib/core/lhpermission/lhrolefunction.php',
        'erLhcoreClassGroupRole'  => 'lib/core/lhpermission/lhgrouprole.php',
        'erLhcoreClassGroupUser'  => 'lib/core/lhuser/lhgroupuser.php',
        'erLhcoreClassUserUtils'  => 'lib/core/lhuser/lhuserutils.php',

        // Translations
        'erTranslationClassLhTranslation' => 'lib/core/lhcore/lhtranslation.php',

        // Core clases models
        'erLhcoreClassUser'         => 'lib/core/lhuser/lhuser.php',
        'erLhcoreClassGroup'        => 'lib/core/lhuser/lhgroup.php',
        'erLhcoreClassChat'         => 'lib/core/lhchat/lhchat.php',
		'erLhcoreClassChatWorkflow' => 'lib/core/lhchat/lhchatworkflow.php',
		'erLhcoreClassChatCleanup'  => 'lib/core/lhchat/lhchatcleanup.php',
        'erLhcoreClassChatValidator'=> 'lib/core/lhchat/lhchatvalidator.php',
        'erLhcoreClassAdminChatValidatorHelper'=> 'lib/core/lhchat/lhchatadminvalidatorhelper.php',
        'erLhcoreClassChatCommand'=> 'lib/core/lhchat/lhchatcommand.php',
        'erLhcoreClassChatHelper' => 'lib/core/lhchat/lhchathelper.php',
        'erLhcoreClassUserValidator' => 'lib/core/lhuser/lhuservalidator.php',
        'Clamav'                     => 'lib/core/lhexternal/Clamav.php',

        'erLhcoreClassBBCode'       => 'lib/core/lhbbcode/lhbbcode.php',
        'erLhcoreClassBBCodePlain'  => 'lib/core/lhbbcode/lhbbcode_cleanup.php',

        'erLhcoreClassDepartament' => 'lib/core/lhdepartament/lhdepartament.php',
        'erLhcoreClassUserDep'      => 'lib/core/lhdepartament/lhuserdep.php',

        'erLhcoreClassRenderHelper' => 'lib/core/lhcore/lhrenderhelper.php',

         // Models
        'erLhcoreClassModelTransfer' => 'lib/models/lhtransfer/erlhcoreclassmodeltransfer.php',


        'erLhcoreClassModelUser'            => 'lib/models/lhuser/erlhcoreclassmodeluser.php',
        'erLhcoreClassModelUserOnlineSession' => 'lib/models/lhuser/erlhcoreclassmodeluseronlinesession.php',
        'erLhcoreClassModelUserSession'     => 'lib/models/lhuser/erlhcoreclassmodelusersession.php',
		'erLhcoreClassModelUserRemember' 	=> 'lib/models/lhuser/erlhcoreclassmodeluserremember.php',
        'erLhcoreClassModelGroup'           => 'lib/models/lhuser/erlhcoreclassmodelgroup.php',
        'erLhcoreClassModelGroupWork'       => 'lib/models/lhuser/erlhcoreclassmodelgroupwork.php',
        'erLhcoreClassModelGroupUser'       => 'lib/models/lhuser/erlhcoreclassmodelgroupuser.php',
        'erLhcoreClassModelGroupObject'     => 'lib/models/lhuser/erlhcoreclassmodelgroupobject.php',
        'erLhcoreClassModelForgotPassword'  => 'lib/models/lhuser/erlhcoreclassmodelforgotpassword.php',

        'erLhcoreClassModelUserSetting'  	   => 'lib/models/lhuser/erlhcoreclassmodelusersetting.php',
        'erLhcoreClassModelUserSettingOption'  => 'lib/models/lhuser/erlhcoreclassmodelusersettingoption.php',

        'erLhcoreClassModelGroupRole'   => 'lib/models/lhpermission/erlhcoreclassmodelgrouprole.php',
        'erLhcoreClassModelChat'        => 'lib/models/lhchat/erlhcoreclassmodelchat.php',
        'erLhcoreClassModelChatPaid'    => 'lib/models/lhchat/erlhcoreclassmodelchatpaid.php',
        'erLhcoreClassModelmsg'         => 'lib/models/lhchat/erlhcoreclassmodelmsg.php',
        'erLhcoreClassModelChatStartSettings'         => 'lib/models/lhchat/erlhcoreclassmodelchatstartsettings.php',
    
        'erLhcoreClassModelCannedMsg'           => 'lib/models/lhchat/erlhcoreclassmodelcannedmsg.php',
        'erLhcoreClassModelCannedMsgTag'        => 'lib/models/lhchat/erlhcoreclassmodelcannedmsgtag.php',
        'erLhcoreClassModelCannedMsgTagLink'    => 'lib/models/lhchat/erlhcoreclassmodelcannedmsgtaglink.php',
    
        'erLhcoreClassModelChatConfig'  => 'lib/models/lhchat/erlhcoreclassmodelchatconfig.php',

        'erLhcoreClassModelChatOnlineUser'   		=> 'lib/models/lhchat/erlhcoreclassmodelchatonlineuser.php',
        'erLhcoreClassModelChatOnlineUserFootprint' => 'lib/models/lhchat/erlhcoreclassmodelchatonlineuserfootprint.php',
        'erLhcoreClassModelChatBlockedUser'  		=> 'lib/models/lhchat/erlhcoreclassmodelchatblockeduser.php',
        'erLhcoreClassModelRole'        => 'lib/models/lhpermission/erlhcoreclassmodelrole.php',
        'erLhcoreClassModelRoleFunction'=> 'lib/models/lhpermission/erlhcoreclassmodelrolefunction.php',
        'erLhcoreClassModelDepartament' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartament.php',
        'erLhcoreClassModelDepartamentAvailability' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentavailability.php',
        'erLhcoreClassModelDepartamentGroup' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentgroup.php',
        'erLhcoreClassModelDepartamentLimitGroup' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentlimitgroup.php',
        'erLhcoreClassModelDepartamentGroupMember' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentgroupmember.php',
        'erLhcoreClassModelDepartamentLimitGroupMember' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentlimitgroupmember.php',
        'erLhcoreClassModelDepartamentGroupUser' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentgroupuser.php',
        'erLhcoreClassModelDepartamentCustomWorkHours' => 'lib/models/lhdepartament/erlhcoreclassmodeldepartamentcustomworkhours.php',
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
		'erLhAbstractModelEmailTemplate' 			      => 'lib/models/lhabstract/erlhabstractmodelemailtemplate.php',
		'erLhAbstractModelProactiveChatInvitation'        => 'lib/models/lhabstract/erlhabstractmodeleproactivechatinvitation.php',
		'erLhAbstractModelProactiveChatInvitationEvent'   => 'lib/models/lhabstract/erlhabstractmodeleproactivechatinvitationevent.php',
		'erLhAbstractModelProactiveChatVariables'           => 'lib/models/lhabstract/erlhabstractmodeleproactivechatvariables.php',
		'erLhAbstractModelProactiveChatEvent'               => 'lib/models/lhabstract/erlhabstractmodeleproactivechatevent.php',
		'erLhAbstractModelProactiveChatCampaign'            => 'lib/models/lhabstract/erlhabstractmodeleproactivechatcampaign.php',
		'erLhAbstractModelProactiveChatCampaignConversion'  => 'lib/models/lhabstract/erlhabstractmodelproactivechatcampaignconv.php',

		'erLhAbstractModelAutoResponder'  			=> 'lib/models/lhabstract/erlhabstractmodelautoresponder.php',
		'erLhAbstractModelAutoResponderChat'  		=> 'lib/models/lhabstract/erlhabstractmodelautoresponderchat.php',
		'erLhAbstractModelChatVariable'  		    => 'lib/models/lhabstract/erlhabstractmodelchatvariable.php',
		'erLhAbstractModelChatColumn'  		        => 'lib/models/lhabstract/erlhabstractmodelchatcolumn.php',
		'erLhAbstractModelChatPriority'  		    => 'lib/models/lhabstract/erlhabstractmodelchatpriority.php',

		'erLhAbstractModelSurvey'  			        => 'lib/models/lhabstract/erlhabstractmodelsurvey.php',
		'erLhAbstractModelSurveyItem'  			    => 'lib/models/lhabstract/erlhabstractmodelsurveyitem.php',
		'erLhcoreClassSurveyValidator'  			=> 'lib/core/lhsurvey/lhsurveyvalidator.php',
		'erLhcoreClassSurveyExporter'  			    => 'lib/core/lhsurvey/lhsurveyexporter.php',
		'erLhAbstractModelBrowseOfferInvitation'  	=> 'lib/models/lhabstract/erlhabstractmodelbrowseofferinvitation.php',
		'erLhAbstractModelForm'  					=> 'lib/models/lhabstract/erlhabstractmodelform.php',
		'erLhAbstractModelFormCollected'  			=> 'lib/models/lhabstract/erlhabstractmodelformcollected.php',
		'erLhAbstractModelWidgetTheme'  			=> 'lib/models/lhabstract/erlhabstractmodelwidgettheme.php',
		'erLhAbstractModelAdminTheme'  			    => 'lib/models/lhabstract/erlhabstractmodeladmintheme.php',
		'erLhcoreClassThemeValidator'  			    => 'lib/core/lhtheme/lhthemevalidator.php',

		'erLhAbstractModelProduct'  			    => 'lib/models/lhabstract/erlhabstractmodelproduct.php',
		'erLhAbstractModelProductDepartament'  		=> 'lib/models/lhabstract/erlhabstractmodelproductdepartament.php',
		'erLhAbstractModelRestAPIKey'  			    => 'lib/models/lhabstract/erlhabstractmodelrestapikey.php',
		'erLhAbstractModelRestAPIKeyRemote'  		=> 'lib/models/lhabstract/erlhabstractmodelrestapikeyremote.php',

		'erLhcoreClassFormRenderer'  		=> 'lib/core/lhform/lhformrenderer.php',
		'erLhcoreClassAbstract' 			=> 'lib/core/lhabstract/lhabstract.php',
		'erLhcoreClassChatMail' 		 	=> 'lib/core/lhchat/lhchatmail.php',

		// Subject
	    'erLhAbstractModelSubject'	        => 'lib/models/lhabstract/erlhabstractmodelsubject.php',
	    'erLhAbstractModelSubjectDepartment'=> 'lib/models/lhabstract/erlhabstractmodelsubjectdepartment.php',
	    'erLhAbstractModelSubjectChat'      => 'lib/models/lhabstract/erlhabstractmodelsubjectchat.php',
	    'erLhAbstractModelAudit'            => 'lib/models/lhabstract/erlhabstractmodelaudit.php',

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
		'erLhcoreClassChatArcive' 		        => 'lib/core/lhchat/lhchatarchive.php',

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
		
		'Mobile_Detect'								=> 'lib/core/lhexternal/Mobile_Detect.php',
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

		'lhCountries'						    	=> 'lib/core/lhexternal/lhcountries.php',

        // Speech
        'erLhcoreClassSpeech'                       => 'lib/core/lhspeech/lhspeech.php',
        'erLhcoreClassModelSpeechLanguage'          => 'lib/models/lhspeech/erlhcoreclassmodelspeechlanguage.php',
        'erLhcoreClassModelSpeechLanguageDialect'   => 'lib/models/lhspeech/erlhcoreclassmodelspeechlanguagedialect.php',
        'erLhcoreClassModelSpeechChatLanguage'      => 'lib/models/lhspeech/erlhcoreclassmodelspeechchatlanguage.php',
        'erLhcoreClassModelSpeechUserLanguage'      => 'lib/models/lhspeech/erlhcoreclassmodelspeechuserlanguage.php',

		// Co browse
		'erLhcoreClassCoBrowse' 					=> 'lib/core/lhcobrowse/lhcobrowse.php',
		'erLhcoreClassModelCoBrowse' 				=> 'lib/models/lhcobrowse/erlhcoreclassmodelcobrowse.php',

        // Translations
        'erLhcoreClassTranslate'                    => 'lib/core/lhtranslate/lhtranslate.php',
        'erLhcoreClassTranslateBing'                => 'lib/core/lhtranslate/lhbingtranslate.php',
        'erLhcoreClassTranslateGoogle'              => 'lib/core/lhtranslate/lhgoogletranslate.php',
        'erLhcoreClassTranslateYandex'              => 'lib/core/lhtranslate/lhyandextranslate.php',
        
		'erLhcoreClassDBTrait'       				=> 'lib/core/lhcore/lhdbtrait.php',
		'lhSecurity'       				            => 'lib/core/lhexternal/lhsecurity.php',
		'erLhcoreClassChatPaid'       				=> 'lib/core/lhchat/lhchatpaid.php',

		'erLhcoreClassChatEvent'       				=> 'lib/core/lhchat/lhchatevent.php',

        // Bot classes
        'erLhcoreClassModelGenericBotBot'               => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotbot.php',
        'erLhcoreClassModelGenericBotException'         => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotexception.php',
        'erLhcoreClassModelGenericBotExceptionMessage'  => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotexceptionmessage.php',

        'erLhcoreClassModelGenericBotGroup'         => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotgroup.php',
        'erLhcoreClassModelGenericBotTrigger'       => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbottrigger.php',
        'erLhcoreClassModelGenericBotTriggerEvent'  => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbottriggerevent.php',
        'erLhcoreClassModelGenericBotPayload'       => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotpayload.php',
        'erLhcoreClassModelGenericBotChatWorkflow'  => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotchatworkflow.php',
        'erLhcoreClassModelGenericBotChatEvent'     => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotchatevent.php',
        'erLhcoreClassModelGenericBotPendingEvent'  => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotpendingevent.php',
        'erLhcoreClassModelGenericBotRepeatRestrict'=> 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotrepeatrestrict.php',

        'erLhcoreClassModelGenericBotTrItem'        => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbottritem.php',
        'erLhcoreClassModelGenericBotTrGroup'       => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbottrgroup.php',

        'erLhcoreClassGenericBot'                   => 'lib/core/lhgenericbot/lhgenericbot.php',
        'erLhcoreClassGenericBotWorkflow'           => 'lib/core/lhgenericbot/lhgenericbotworkflow.php',
        'erLhcoreClassGenericBotValidator'          => 'lib/core/lhgenericbot/lhgenericbotvalidator.php',
        'erLhcoreClassGenericBotException'          => 'lib/core/lhgenericbot/lhgenericbotexception.php',

        'erLhcoreClassGenericBotActionText'         => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactiontext.php',
        'erLhcoreClassGenericBotActionButtons'      => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionbuttons.php',
        'erLhcoreClassGenericBotUpdateActions'      => 'lib/core/lhgenericbot/actionsChat/lhgenericbotupdateactions.php',
        'erLhcoreClassGenericBotActionCollectable'  => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactioncollectable.php',
        'erLhcoreClassGenericBotActionList'         => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionlist.php',
        'erLhcoreClassGenericBotActionGeneric'      => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactiongeneric.php',
        'erLhcoreClassGenericBotActionCommand'      => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactioncommand.php',
        'erLhcoreClassGenericBotActionPredefined'   => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionpredefined.php',
        'erLhcoreClassGenericBotActionTyping'       => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactiontyping.php',
        'erLhcoreClassGenericBotActionProgress'     => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionprogress.php',
        'erLhcoreClassGenericBotActionVideo'        => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionvideo.php',
        'erLhcoreClassGenericBotActionExecute_js'     => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionexecute_js.php',
        'erLhcoreClassGenericBotActionAttribute'    => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionattribute.php',
        'erLhcoreClassGenericBotActionActions'      => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionactions.php',
        'erLhcoreClassGenericBotActionIntent'       => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionintent.php',
        'erLhcoreClassGenericBotActionIntentcheck'  => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionintentcheck.php',
        'erLhcoreClassGenericBotActionConditions'   => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionconditions.php',
        'erLhcoreClassGenericBotActionMatch_actions'=> 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionmatch_actions.php',
        'erLhcoreClassGenericBotActionEvent_type'   => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionevent_type.php',
        'erLhcoreClassGenericBotActionRepeat_restrict'   => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionrepeat_restrict.php',
        'erLhcoreClassGenericBotActionRestapi'      => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactionrestapi.php',
        'erLhcoreClassGenericBotActionTbody'        => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactiontbody.php',
        'erLhcoreClassGenericBotActionText_conditional' => 'lib/core/lhgenericbot/actionTypes/lhgenericbotactiontext_conditional.php',
        'erLhcoreClassModelGenericBotCommand'       => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotcommand.php',

        'erLhcoreClassModelGenericBotRestAPI'       => 'lib/models/lhgenericbot/erlhcoreclassmodelgenericbotrestapi.php',
        'erLhcoreClassLHCBotWorker'                 => 'lib/core/lhgenericbot/lhgenericbotworker.php',

        // Group chat
        'erLhcoreClassModelGroupChat'               => 'lib/models/lhchat/erlhcoreclassmodelgroupchat.php',
        'erLhcoreClassGroupChat'                    => 'lib/core/lhchat/lhgroupchat.php',
        'erLhcoreClassModelGroupMsg'                => 'lib/models/lhchat/erlhcoreclassmodelgroupmsg.php',
        'erLhcoreClassModelGroupChatMember'         => 'lib/models/lhchat/erlhcoreclassmodelgroupchatmember.php',

        // Group chat archive tables
        'erLhcoreClassModelGroupChatArchive' 		=> 'lib/models/lhchat/erlhcoreclassmodelgroupchatarchive.php',
        'erLhcoreClassModelGroupChatMemberArchive' 	=> 'lib/models/lhchat/erlhcoreclassmodelgroupchatmemberarchive.php',
        'erLhcoreClassModelGroupMsgArchive' 		=> 'lib/models/lhchat/erlhcoreclassmodelgroupmsgarchive.php',

        // Notifications
        'erLhcoreClassNotifications'                => 'lib/core/lhnotifications/lhnotifications.php',
        'erLhcoreClassModelNotificationSubscriber'  => 'lib/models/lhnotifications/erlhcoreclassmodelnotificationsubscriber.php',
        'erLhcoreClassLHCMobile'                    => 'lib/core/lhmobile/lhmobile.php',

        // Web hooks
        'erLhcoreClassChatWebhookHttp'              => 'lib/core/lhchat/lhchatwebhookhttp.php',
        'erLhcoreClassModelChatWebhook'             => 'lib/models/lhchat/erlhcoreclassmodelchatwebhook.php',
        'erLhcoreClassChatWebhookResque'            => 'lib/core/lhchat/lhchatwebhookresque.php',

),
include('var/autoloads/lhextension_autoload.php')
);

?>
