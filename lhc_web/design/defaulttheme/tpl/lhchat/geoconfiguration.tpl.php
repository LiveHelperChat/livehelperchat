<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<form action="" method="post">

<label><input type="checkbox" id="id_GeoDetectionEnabled" name="GeoDetectionEnabled" value="on" <?php isset($geo_data['geo_detection_enabled']) && $geo_data['geo_detection_enabled'] == 1 ? print 'checked="checked"' : ''?> /> GEO Enabled</label>
<br />

<ul class="accordion<?php (!isset($geo_data['geo_detection_enabled']) || $geo_data['geo_detection_enabled'] == 0) ? print ' hide' : '' ?>" id="settings-geo">
  <li <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'mod_geoip2') ? print 'class="active"' : ''?> >
    <div class="title">
      <h5>mod_geoip2</h5>
    </div>
    <div class="content">
        <label><input type="radio" name="UseGeoIP" value="mod_geoip2" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'mod_geoip2') ? print 'checked="checked"' : '' ?> /> Use mod_geoip2</label>
            
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Country code server variable'); ?></label>
        <input type="text" name="ServerVariableGEOIP_COUNTRY_CODE" value="<?php isset($geo_data['mod_geo_ip_country_code']) ? print 'checked="checked"' : print 'GEOIP_COUNTRY_CODE' ?>">
        
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Country name server variable'); ?></label>
        <input type="text" name="ServerVariableGEOIP_COUNTRY_NAME" value="<?php isset($geo_data['mod_geo_ip_country_name']) ? print 'checked="checked"' : print 'GEOIP_COUNTRY_NAME' ?>">
        
        <input type="submit" class="button small round" name="StoreGeoIPConfiguration" value="Save" />
                
    </div>
  </li>
  <li <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'locatorhq') ? print 'class="active"' : ''?> >
    <div class="title">
        <h5>http://www.locatorhq.com <a target="_blank" href="http://www.locatorhq.com">See service terms and conditions</a></h5>
    </div>
    <div class="content">
    
     <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Requests will be comming from');?> - <?php echo $_SERVER['SERVER_ADDR']; ?></p>
    
     <label class="inline"><input type="radio" name="UseGeoIP" value="locatorhq" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'locatorhq') ? print 'checked="checked"' : '' ?> /> Use this service</label>
     
     <label>API Key</label>
     <input type="text" name="locatorhqAPIKey" value="<?php isset($geo_data['locatorhq_api_key']) ? print htmlspecialchars($geo_data['locatorhq_api_key']) : print '' ?>">
     
     <label>Username</label>
     <input type="text" name="locatorhqUsername" value="<?php isset($geo_data['locatorhqusername']) ? print htmlspecialchars($geo_data['locatorhqusername']) : print '' ?>">
             
     <input type="submit" class="button small round" name="StoreGeoIPConfiguration" value="Save" />
    
    </div>
  </li>
  <li <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'freegeoip') ? print 'class="active"' : ''?> >
    <div class="title">
      <h5>http://freegeoip.net/static/index.html - <a target="_blank" href="http://freegeoip.net/static/index.html">See service terms and conditions</a></h5>
    </div>
    <div class="content">

      <label class="inline"><input type="radio" name="UseGeoIP" value="freegeoip" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'freegeoip') ? print 'checked="checked"' : '' ?> /> Use this service</label>
      
      <input type="submit" class="button small round" name="StoreGeoIPConfiguration" value="Save" />

    </div>
  </li>
</ul>

<input type="submit" class="button small round" name="StoreGeoIPConfiguration" value="Save" />

</form>

<script>
$('#id_GeoDetectionEnabled').change(function(){
    if ($(this).is(':checked')){
        $('#settings-geo').removeClass('hide');
    } else {
        $('#settings-geo').addClass('hide');
    }
});
</script>