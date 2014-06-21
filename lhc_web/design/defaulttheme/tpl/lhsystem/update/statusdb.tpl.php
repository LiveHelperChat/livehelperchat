<div class="row">
	<div class="columns small-12">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Database structure check')?></h3>
		<ul class="circle fs12">
		<?php 
		$hasError = false;
		$queries = array();
		foreach ($tables as $table => $status) :
		$queries = array_merge($queries,$status['queries']);
		$hasError = $status['error'] == true ? true : $hasError;
		if ($status['error'] == true) : ?>
			<li><label class="<?php echo $status['error'] == false ? 'success' : 'alert'?> label"><?php echo $table?> - <?php echo $status['status']?></label></li>
		<?php endif; endforeach;?>
		</ul>
		<?php if ($hasError == false) : ?>
			<label class="success label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Your database does not require any updates')?></label>
		<?php endif; ?>
	</div>
	<?php if ( !empty($queries) ) : ?>
	<div class="columns small-12">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Queries which will be executed on update')?></h3>
		<ul class="fs11 circle">
			<?php foreach ($queries as $query) : ?>
				<li class="fs11"><?php echo $query;?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</div>

<?php if ($hasError) : ?>
<a class="button radius small" onclick="updateDatabase()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Update database')?></a>
<?php endif;?>