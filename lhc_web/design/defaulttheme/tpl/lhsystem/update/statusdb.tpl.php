<div class="hide" id="db-status-updating">
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updating...'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
</div>

<div class="row" id="db-status-checked">
	<div class="col-md-12 form-group">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Database structure check')?></h3>
		<ul>
		<?php 
		$hasError = false;
		$queries = array();
		foreach ($tables as $table => $status) :
		$queries = array_merge($queries,$status['queries']);
		$hasError = $status['error'] == true ? true : $hasError;
		if ($status['error'] == true) : ?>
			<li><div class="alert alert-<?php echo $status['error'] == false ? 'success' : 'danger'?>"><?php echo $table?> - <?php echo $status['status']?></div></li>
		<?php endif; endforeach;?>
		</ul>
				
		<?php if ($hasError == false) : ?>
			<label class="alert alert-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Your database does not require any updates')?></label>
		<?php endif; ?>
		
		<?php if ($hasError) : ?>
		<a class="btn btn-primary" onclick="updateDatabase()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Update database')?></a>
		<?php endif;?>

	</div>
	<?php if ( !empty($queries) ) : ?>
	<div class="col-md-12">	
		<div class="panel panel-default">
          <div class="panel-heading"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('update/statusdb','Queries which will be executed on update')?></div>
          <div class="panel-body">
            <ul>
        		<?php foreach ($queries as $query) : ?>
        			<li><?php echo $query;?></li>
        		<?php endforeach; ?>
        	</ul>
          </div>
        </div>	
	</div>
	<?php endif; ?>
</div>

