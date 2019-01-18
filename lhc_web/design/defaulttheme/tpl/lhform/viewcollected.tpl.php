<h1><?php echo htmlspecialchars($collected->form)?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','collected information');?></h1>

<?php echo $content?>

<hr>
<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Identifier');?></h3>
<?php echo htmlspecialchars($collected->identifier)?>
<hr>
<a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $collected->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Download');?></a>