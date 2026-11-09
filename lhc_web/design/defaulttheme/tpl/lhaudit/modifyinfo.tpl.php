<?php if (!empty($item->updated_at)) : ?>
	<?php
	$modifiedUser = !empty($item->muser_id) ? erLhcoreClassModelUser::fetch($item->muser_id) : false;
	$details = array();
	if (!empty($item->created_at)) {
		$details[] = erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo', 'Created') . ': ' . date('Y-m-d H:i:s', (int)$item->created_at);
	}
	$details[] = erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo', 'Modified by') . ': ' . ($modifiedUser ? (string)$modifiedUser : erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo', 'Unknown'));
	$details[] = erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo', 'Modified') . ': ' . date('Y-m-d H:i:s', (int)$item->updated_at);
	?>
	<span class="badge bg-light text-muted mx-1 fs11" title="<?php echo htmlspecialchars(implode("\n", $details), ENT_QUOTES, 'UTF-8'); ?>">
		<i class="material-icons align-middle" aria-hidden="true">schedule</i>
		<?php echo erLhcoreClassChat::formatSeconds(max(0, time() - (int)$item->updated_at)); ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/modifyinfo', 'ago'); ?>
	</span>
<?php unset($details);endif; ?>
