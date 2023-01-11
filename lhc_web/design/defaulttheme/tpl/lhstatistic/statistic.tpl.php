<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/statistic.tpl.php'));?>

<div class="row">
    <div translate="no" class="col-2 border-right pe-0 ps-0">
        <div class="w-100 d-flex flex-column flex-grow-1">
            <table class="table table-sm mb-0 table-small" ng-non-bindable>
                <thead>
                <tr>
                    <th width="99%">
                        <span title="Name" class="material-icons">saved_search</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','My reports');?>
                    </th>
                    <th width="1%" nowrap="nowrap">
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (\LiveHelperChat\Models\Statistic\SavedReport::getList(['limit' => false, 'filter' => ['user_id' =>  erLhcoreClassUser::instance()->getUserID()]]) as $report) : ?>
                    <tr>
                        <td>
                            <a <?php if (isset($input->report) && $input->report == $report->id) : ?>class="fw-bold"<?php endif; ?> href="<?php echo erLhcoreClassDesign::baseurl('statistic/loadreport')?>/<?php echo $report->id?>" title="<?php echo htmlspecialchars($report->description)?>"><?php echo htmlspecialchars($report->name)?></a>
                        </td>
                        <td>
                            <div class="btn-group">
                                <i class="material-icons settings text-muted fs14" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">menu</i>
                                <div class="dropdown-menu py-0 fs13">
                                    <a class="dropdown-item text-muted px-2 csfr-required csfr-post" href="<?php echo erLhcoreClassDesign::baseurl('statistic/copyreport')?>/<?php echo $report->id?>"><span class="material-icons">content_copy</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Make a copy');?></a>
                                    <a class="dropdown-item text-muted px-2 csfr-required csfr-post" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('statistic/deletereport')?>/<?php echo $report->id?>"><span class="material-icons">delete</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete');?></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>
        </div>
    </div>
    <div class="col-10" id="view-content">
        <div role="tabpanel" ng-non-bindable>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'active') : ?> active<?php endif;?>" onclick="redrawAllCharts(500)" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/active"><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_statistic.tpl.php'));?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'chatsstatistic') : ?> active<?php endif;?>" onclick="redrawAllCharts(500)" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/chatsstatistic"><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_chats_statistic.tpl.php'));?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'total') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/total"><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_total_statistic.tpl.php'));?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'last24') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/last24" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_last_24_hours_statistic.tpl.php'));?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'agentstatistic') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/agentstatistic" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_agent_statistic.tpl.php'));?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'performance') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/performance" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_performance.tpl.php'));?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'departments') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/departments" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_departments.tpl.php'));?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'visitors') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/visitors" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_visitors.tpl.php'));?></a></li>

        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','configuration')) : ?>
		<li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'configuration') : ?> active<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/configuration" ><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/tab_configuration.tpl.php'));?></a></li>
        <?php endif; ?>

		<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/statistic_tab_multiinclude.tpl.php')); ?>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content statistic-tab">
	    
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

		<?php if ($tab == 'departments') : ?>
        <div role="tabpanel" class="tab-pane active">
          <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/departments.tpl.php'));?>
        </div>
        <?php endif;?>

		<?php if ($tab == 'configuration') : ?>
        <div role="tabpanel" class="tab-pane active">
          <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/configuration.tpl.php'));?>
        </div>
        <?php endif;?>

		<?php if ($tab == 'visitors') : ?>
        <div role="tabpanel" class="tab-pane active">
          <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/visitors.tpl.php'));?>
        </div>
        <?php endif;?>

		<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/statistic_tab_content_multiinclude.tpl.php')); ?>
		
	</div>
</div>
    </div>
</div>