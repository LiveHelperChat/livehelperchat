<h1><?php echo htmlspecialchars($survey)?></h1>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" <?php if ($tab == 'list' || $tab == '') : ?>class="active"<?php endif;?>><a href="#list" aria-controls="list" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','List')?></a></li>
		<li role="presentation" <?php if ($tab == 'chart') : ?>class="active"<?php endif;?>><a href="#chart" aria-controls="chart" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chart')?></a></li>
	</ul>

    <!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="list">
		  <?php include(erLhcoreClassDesign::designtpl('lhsurvey/collected/search_panel.tpl.php')); ?>
          <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names_enabled.tpl.php'));?>
		  <?php include(erLhcoreClassDesign::designtpl('lhsurvey/collected/list.tpl.php')); ?>
		</div>
		<div role="tabpanel" class="tab-pane" id="chart">
		  <?php include(erLhcoreClassDesign::designtpl('lhsurvey/collected/chart.tpl.php')); ?>
		</div>
	</div>
</div>