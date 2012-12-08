<div class="ui-widget">
			<div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span> 
				<?=erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','You do not have permission to access module');?> <strong>&quot;<?=$module['module']['name']?>&quot;</strong> <?=erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','and use');?> <strong>&quot;<?=implode(',',$module['module']['functions'])?>&quot;</strong> <?=erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','function');?>
				</p>
			</div>
</div>
