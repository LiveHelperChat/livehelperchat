<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','AI MCP Server');?></h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off" ng-non-bindable>

	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','MCP server name');?></label>
		<input type="text" class="form-control" name="server_name" value="<?php echo htmlspecialchars($mcp_server_name)?>" />
		<small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Name reported to the client during the initialize handshake. Default is Live Helper Chat.');?></small>
	</div>

	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','MCP endpoint URL');?></label>
		<input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($mcp_endpoint)?>" />
	</div>

	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Access token');?></label>
		<input type="text" class="form-control" name="token" value="<?php echo htmlspecialchars($mcp_token)?>" autocomplete="off" />
		<small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','The endpoint is disabled until a token is set.');?></small>
	</div>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
	<input type="submit" class="btn btn-secondary" name="GenerateToken" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Generate new token'); ?>" />

</form>

<?php if (!empty($mcp_tools)) : ?>
<h3 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Tools exposed to the client');?></h3>

<table class="table table-sm table-hover" cellpadding="0" cellspacing="0" ng-non-bindable>
	<thead>
		<tr>
			<th width="30%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Tool');?></th>
			<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key','Description');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($mcp_tools as $tool) : ?>
		<tr>
			<td>
				<code><?php echo htmlspecialchars($tool['name'])?></code>
				<div class="text-muted small"><?php echo htmlspecialchars($tool['handler'])?></div>
			</td>
			<td class="small"><?php echo htmlspecialchars($tool['description'])?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
