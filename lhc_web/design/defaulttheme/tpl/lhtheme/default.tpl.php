<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Default theme');?></h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Default theme was set'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off" enctype="multipart/form-data">

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<div class="form-group" ng-non-bindable>
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Theme, you can choose multiple to show random one.')?></label>
        <select name="ThemeID[]" class="form-control form-control-sm" multiple size="5">
            <option value="0">--<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Default');?>--</option>
    		<?php foreach (erLhAbstractModelWidgetTheme::getList(array('limit' => false, 'sort' => '`name` ASC')) as $theme) : ?>
    		   <option value="<?php echo $theme->id?>" <?php in_array($theme->id,$default_theme_id) ? print 'selected="selected"' : '';?>><?php echo htmlspecialchars($theme->name)?></option>
    		<?php endforeach; ?>
    	</select>
	</div>

    <div class="form-group" ng-non-bindable>
        <label>
            <input type="checkbox" name="department_default" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','As default department theme')?>
        </label>
    </div>

	<input type="submit" name="ImportTheme" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/default','Set as default theme')?>" />
	
</form>
	