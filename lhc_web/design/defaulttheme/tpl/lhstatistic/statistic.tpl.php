<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/statistic.tpl.php'));?>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" <?php if ($tab == 'active') : ?>class="active"<?php endif;?>><a onclick="redrawAllCharts(500)" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/active"><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_statistic.tpl.php'));?></a></li>
		<li role="presentation" <?php if ($tab == 'chatsstatistic') : ?>class="active"<?php endif;?>><a onclick="redrawAllCharts(500)" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/chatsstatistic"><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_chats_statistic.tpl.php'));?></a></li>
		<li role="presentation" <?php if ($tab == 'total') : ?>class="active"<?php endif;?>><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/total"><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_total_statistic.tpl.php'));?></a></li>
		<li role="presentation" <?php if ($tab == 'last24') : ?>class="active"<?php endif;?>><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/last24" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_last_24_hours_statistic.tpl.php'));?></a></li>
		<li role="presentation" <?php if ($tab == 'agentstatistic') : ?>class="active"<?php endif;?>><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/agentstatistic" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_agent_statistic.tpl.php'));?></a></li>
		<li role="presentation" <?php if ($tab == 'performance') : ?>class="active"<?php endif;?>><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/performance" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_performance.tpl.php'));?></a></li>
		<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/statistic_tab_multiinclude.tpl.php')); ?>
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
		
		<?php if ($tab == 'agentstatistic') : ?>
        <div role="tabpanel" class="tab-pane active">
          <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/agentstatistic.tpl.php'));?>
        </div>
        <?php endif;?>
		
		<?php if ($tab == 'performance') : ?>
        <div role="tabpanel" class="tab-pane active">
          <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/performance.tpl.php'));?>
        </div>
        <?php endif;?>

		<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/statistic_tab_content_multiinclude.tpl.php')); ?>
		
	</div>
</div>