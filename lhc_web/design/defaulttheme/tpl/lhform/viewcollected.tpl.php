<h1><?php echo htmlspecialchars($collected->form)?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','collected information');?></h1>

<?php echo $content?>

<a class="small radius button" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $collected->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Download');?></a>