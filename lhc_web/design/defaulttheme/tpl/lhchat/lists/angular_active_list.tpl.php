<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/active.tpl.php'));?>
				
<div ng-if="active_chats.list.length == 0" class="m10 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>