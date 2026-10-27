<h1><?php echo htmlspecialchars($collected->form)?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','collected information');?></h1>

<div  ng-non-bindable>
<?php echo $content?>
</div>

<?php $canEditUsers = erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'edituser'); ?>

<?php if ((int)$collected->form->agent_evaluation === 1) : ?>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Agent');?></h4>
<?php if ($collected->evaluated_user instanceof erLhcoreClassModelUser) : ?>
    <?php if ($canEditUsers) : ?><a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $collected->evaluated_user->id?>"><?php endif; ?><?php echo htmlspecialchars($collected->evaluated_user->name_official)?><?php if ($canEditUsers) : ?></a><?php endif; ?>
<?php elseif ($collected->evaluated_user_id > 0) : ?>
    <?php echo htmlspecialchars((string)$collected->evaluated_user_id)?>
<?php else : ?>
    n/a
<?php endif; ?>
<?php endif; ?>

<?php if ($collected->user instanceof erLhcoreClassModelUser) : ?>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/viewcollected','Filled by');?></h4>
<?php if ($canEditUsers) : ?><a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $collected->user->id?>"><?php endif; ?><?php echo htmlspecialchars($collected->user->name_official)?><?php if ($canEditUsers) : ?></a><?php endif; ?>
<?php endif; ?>

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