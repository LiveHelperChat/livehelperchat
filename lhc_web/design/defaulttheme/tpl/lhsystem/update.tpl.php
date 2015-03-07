<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Live Helper Chat update');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
	<div class="col-md-4">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Your version')?> - <?php echo erLhcoreClassUpdate::LHC_RELEASE/100;?></h3>
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Current version')?> - <span class="success-color" id="recent-version">...</span></h3>
		<a class="btn btn-default btn-xs" href="http://livehelperchat.com/news-5c.html" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','News')?></a>
		<a class="btn btn-default btn-xs" href="http://livehelperchat.com/article/view/63" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Update instructions')?></a>
		
	</div>
	<div class="col-md-8">
		<div id="status-db"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Comparing current database structure, please wait...')?></div>
	</div>
</div>


<script>
function updateDatabase() {
	$('#db-status-checked').hide();
	$('#db-status-updating').show();		
	$.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdbdoupdate',function(data){
        $('#status-db').html(data.result);            
    }); 
};

(function() {
	
  $.ajax({
      url: 'https://livehelperchat.com/update/version',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallbackLHC',
      success: function(data){        
              $('#recent-version').text(data.version/100); 
      }
  });
	 
  $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdb',function(data){
      $('#status-db').html(data.result);            
  });
    
})();
</script>