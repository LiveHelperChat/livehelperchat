<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Live Helper Chat update');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
	<div class="columns small-6">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Your version')?> - <?php echo erLhcoreClassUpdate::LHC_RELEASE/100;?></h3>
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Current version')?> - <span class="success-color" id="recent-version">...</span></h3>
		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Last database update')?> - update_<?php echo erLhcoreClassUpdate::DB_VERSION?>.sql</h4>		
		<a class="button radius radius small" href="http://livehelperchat.com/news-5c.html" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','News')?></a>
		
	</div>
	<div class="columns small-6">
		<div id="database-status"></div>
	</div>
</div>

<script>
(function() {
  $.ajax({
      url: 'https://api.github.com/repos/remdex/livehelperchat/contents/lhc_web/doc/update_db',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallback',
      success: function(data){
          $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/comparedb',{data:data},function(data){
              $('#database-status').html(data.result);            
          });
      }
  }); 
    
  $.ajax({
      url: 'http://livehelperchat.com/update/version',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallback',
      success: function(data){        
              $('#recent-version').text(data.version/100); 
      }
  });    
})();
</script>