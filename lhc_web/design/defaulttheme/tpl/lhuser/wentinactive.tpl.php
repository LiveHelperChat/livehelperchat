<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('user/wentinactive','Offline'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/wentinactive','Because of inactivity you went offline, click continue to go online.');?></p>

<button type="button" class="btn btn-success" data-dismiss="modal" aria-label="Close"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/wentinactive','Continue');?></button>

<a class="btn btn-default btn-warning pull-right" href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/wentinactive','Logout');?></a>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>