<h1><?php echo htmlspecialchars($collected->form)?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','collected information');?></h1>

<div  ng-non-bindable>
<?php echo $content?>
</div>

<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Identifier');?></h4>
<?php echo htmlspecialchars($collected->identifier)?>
<hr>

<?php $collected->custom_fields_array; if (!empty($collected->custom_fields_array)) : ?>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Custom attributes');?></h4>
<ul>
<?php foreach ($collected->custom_fields_array as $fieldData) : ?>
    <li><?php echo htmlspecialchars($fieldData['name'])?> - <?php echo htmlspecialchars($fieldData['value'])?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','identifier');?> - <?php echo htmlspecialchars($fieldData['identifier'])?></li>
<?php endforeach; ?>
</ul>
<hr>
<?php endif; ?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $collected->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Download');?></a>