<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Live Helper Chat update');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
	<div class="columns small-4">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Your version')?> - <?php echo erLhcoreClassUpdate::LHC_RELEASE/100;?></h3>
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Current version')?> - <span class="success-color" id="recent-version">...</span></h3>
		<a class="button radius radius small" href="http://livehelperchat.com/news-5c.html" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','News')?></a>
		<a class="button radius radius small" href="http://livehelperchat.com/article/view/63" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Update instructions')?></a>
		
	</div>
	<div class="columns small-8">
		<div id="status-db"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Comparing current database structure, please wait...')?></div>
	</div>
</div>


<script>

var updateData = '';
function updateDatabase() {
	$.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdbdoupdate',{data:updateData},function(data){
        $('#status-db').html(data.result);            
    }); 
};

(function() {	      
  $.ajax({
      url: 'http://livehelperchat.com/update/version',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallbackLHC',
      success: function(data){        
              $('#recent-version').text(data.version/100); 
      }
  });

  $.ajax({
      url: 'https://api.github.com/repos/LiveHelperChat/livehelperchat/contents/lhc_web/doc/update_db/structure.json',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallbackGIT',
      success: function(data){    	  
    	  $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdb',{'data': data.data.content},function(data){
    	        $('#status-db').html(data.result);            
    	  });
      }
  });   
    
})();
</script>