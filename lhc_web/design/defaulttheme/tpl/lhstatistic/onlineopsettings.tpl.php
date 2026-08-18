<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">
				<span class="material-icons">settings_applications</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online operators widget settings')?>
			</h4>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Settings updated'); ?>
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
				<script>setTimeout(function(){ location.reload(); },250);</script>
			<?php endif; ?>

			<form action="<?php echo erLhcoreClassDesign::baseurl('statistic/onlineopsettings')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
				<div class="table-responsive">
					<table class="table table-sm table-striped">
						<thead>
							<tr>
								<th width="70%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Column')?></th>
								<th width="30%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Position')?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($columnsForTemplate as $column) : ?>
								<tr>
									<td><label class="mb-0"><input type="checkbox" class="form-check-input me-1" name="online_op_columns[]" value="<?php echo htmlspecialchars($column['identifier'])?>" <?php if ($column['enabled']) : ?>checked="checked"<?php endif; ?> /><?php echo htmlspecialchars($column['translation'])?></label></td>
									<td><input type="number" min="1" class="form-control form-control-sm" name="online_op_position[<?php echo htmlspecialchars($column['identifier'])?>]" value="<?php echo (int)$column['position']?>" /></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<input type="submit" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">
				<p class="float-end text-muted"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Choose which columns to display and set their position (lower number appears first).')?></small></p>
			</form>
		</div>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
	</div>
</div>
