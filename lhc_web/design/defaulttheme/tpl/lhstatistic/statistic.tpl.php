<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/statistic.tpl.php'));?>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" <?php if ($tab == 'active') : ?>class="active"<?php endif;?>><a onclick="redrawAllCharts(500)" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/active" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic');?></a></li>
		<li role="presentation" <?php if ($tab == 'chatsstatistic') : ?>class="active"<?php endif;?>><a onclick="redrawAllCharts(500)" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/chatsstatistic" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats statistic');?></a></li>
		<li role="presentation" <?php if ($tab == 'total') : ?>class="active"<?php endif;?>><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/total"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total statistic');?></a></li>
		<li role="presentation" <?php if ($tab == 'last24') : ?>class="active"<?php endif;?>><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/last24" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last 24 hours statistic');?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
	    
	    <?php if ($tab == 'active') : ?>
		<div role="tabpanel" class="tab-pane active">
		  <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/active.tpl.php'));?>
		</div>
		<?php endif;?>
	    
	    <?php if ($tab == 'chatsstatistic') : ?>
		<div role="tabpanel" class="tab-pane active">
		  <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/chatsstatistic.tpl.php'));?>
		</div>
		<?php endif;?>
		
		<?php if ($tab == 'total') : ?>
		<div role="tabpanel" class="tab-pane active">
		  <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/total.tpl.php'));?>
		</div>
		<?php endif;?>
		
		<?php if ($tab == 'last24') : ?>
		<div role="tabpanel" class="tab-pane active">
		  <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/last24hstatistic.tpl.php'));?>
		</div>
		<?php endif;?>
		
	</div>
</div>