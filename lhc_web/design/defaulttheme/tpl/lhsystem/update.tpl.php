<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Update');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
	<div class="col-md-4">
		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Your version')?> - <?php echo sprintf("%0.2f", erLhcoreClassUpdate::LHC_RELEASE/100);?></h5>
		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Current version')?> - <span class="text-success" id="recent-version">...</span></h5>

        <p class="font-weight-bold">Status/security checks</p>
        <ul>
            <li>Strong secret hash -
            <?php if (strlen(erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' )) < 50) : ?>
                <span class="badge badge-danger">weak</span>
            <?php else : ?>
                <span class="badge badge-success">strong</span>
            <?php endif; ?>
                <a href="">More information</a>
            </li>
            <li>Trusted host -
            <?php if (!is_array(erConfigClassLhConfig::getInstance()->getSetting( 'site', 'trusted_host_patterns', false )) || empty(erConfigClassLhConfig::getInstance()->getSetting( 'site', 'trusted_host_patterns', false ))) : ?>
                <span class="badge badge-danger">not set</span>
            <?php else : ?>
                <span class="badge badge-success">set</span>
            <?php endif; ?>
                <a href="">More information</a>
            </li>
            <li>Site address -
            <?php if (!erConfigClassLhConfig::getInstance()->getSetting( 'site', 'site_address', false )) : ?>
                <span class="badge badge-danger">not set</span>
            <?php else : ?>
                <span class="badge badge-success">set - <?php echo htmlspecialchars(erConfigClassLhConfig::getInstance()->getSetting( 'site', 'site_address', false )); ?></span>
            <?php endif; ?>
                <a href="">More information</a>
            </li>
        </ul>

        <p class="font-weight-bold">More information</p>

        <ul>
            <li><a rel="noreferrer" href="http://livehelperchat.com/news-5c.html" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','News')?></a></li>
            <li><a target="_blank" rel="noreferrer" href="https://doc.livehelperchat.com/">Documentation</a></li>
            <li><a target="_blank" rel="noreferrer" href="https://doc.livehelperchat.com/docs/upgrading"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Upgrade instructions')?></a></li>
            <li><a target="_blank" rel="noreferrer" href="http://livehelperchat.com">Live Helper Chat official website</a></li>
        </ul>

	</div>
	<div class="col-md-8">
        <button type="button" class="btn btn-secondary btn-xs" onclick="compareLocal()">Click to compare with local definition</button>
		<div id="status-db"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Comparing current database structure, please wait...')?></div>
	</div>
</div>

<script>
function updateDatabase(scope) {
	$('#db-status-checked').hide();
	$('#db-status-updating').show();		
	$.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdbdoupdate' + (scope != '' ? '/(scope)/local' : ''),function(data){
        $('#status-db').html(data.result);            
    }); 
};

function compareLocal() {
    $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdb/(scope)/local',function(data){
        $('#status-db').html(data.result);
    });
}

(function() {
	
  $.ajax({
      url: 'https://livehelperchat.com/update/version',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallbackLHC',
      success: function(data){        
              $('#recent-version').text((data.version/100).toFixed(2));
      }
  });
	 
  $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdb',function(data){
      $('#status-db').html(data.result);            
  });
    
})();
</script>