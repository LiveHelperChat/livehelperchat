<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/pending.tpl.php'));?>

<div ng-if="pending_chats.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">&#xfa3c;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Nothing found...')?></div>