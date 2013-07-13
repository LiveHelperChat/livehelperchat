<div class="ui-widget">
			<div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span> 
				<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','You do not have permission to access the module');?> <strong>&quot;<?php echo $module['module']['name']?>&quot;</strong> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','and use');?> <strong>&quot;<?php echo implode(',',$module['module']['functions'])?>&quot;</strong> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','function');?>
				</p>
			</div>
</div>
