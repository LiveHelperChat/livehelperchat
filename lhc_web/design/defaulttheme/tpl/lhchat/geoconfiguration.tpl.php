<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>


<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#geoconfiguration" aria-controls="geoconfiguration" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
		<li role="presentation"><a id="map-activator" href="#mapoptions" aria-controls="mapoptions" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Map location')?></a></li>
	</ul>

	

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="geoconfiguration">
			<form action="" method="post">

                <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

                <label><input type="checkbox" id="id_GeoDetectionEnabled" name="GeoDetectionEnabled" value="on" <?php isset($geo_data['geo_detection_enabled']) && $geo_data['geo_detection_enabled'] == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO Enabled');?></label> <br />
                
				<div role="tabpanel" class="<?php (!isset($geo_data['geo_detection_enabled']) || $geo_data['geo_detection_enabled'] == 0) ? print ' hide' : '' ?>" id="settings-geo">

					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="<?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'freegeoip') ? print 'active' : ''?>"><a href="#freegeoip" aria-controls="freegeoip" role="tab" data-toggle="tab">http://freegeoip.net/static/index.html</a></li>
						<li role="presentation" class="<?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'mod_geoip2') ? print 'active' : ''?>"><a href="#mod_geoip2" aria-controls="mod_geoip2" role="tab" data-toggle="tab">mod_geoip2</a></li>
						<li role="presentation" class="<?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'max_mind') ? print 'active' : ''?>"><a href="#maxmind" aria-controls="maxmind" role="tab" data-toggle="tab">MaxMind</a></li>
						<li role="presentation" class="<?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'php_geoip') ? print 'active' : ''?>"><a href="#phpgeoip" aria-controls="phpgeoip" role="tab" data-toggle="tab">PHP-GeoIP</a></li>
						<li role="presentation" class="<?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'ipinfodbcom') ? print 'active' : ''?>"><a href="#panel3" aria-controls="panel3" role="tab" data-toggle="tab">http://ipinfodb.com</a></li>
						<li role="presentation" class="<?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'locatorhq') ? print 'active' : ''?>"><a href="#panel4" aria-controls="panel4" role="tab" data-toggle="tab">http://www.locatorhq.com</a></li>
					</ul>
	
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'freegeoip') ? print 'active' : ''?>" id="freegeoip">
						      <div class="form-group">
						          <label><input type="radio" name="UseGeoIP" value="freegeoip" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'freegeoip') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Use this service'); ?></label> 
						      </div>
						</div>
						
						<div role="tabpanel" class="tab-pane <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'mod_geoip2') ? print 'active' : ''?>" id="mod_geoip2">
						    <div>
						      <label><input type="radio" name="UseGeoIP" value="mod_geoip2" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'mod_geoip2') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Use mod_geoip2'); ?></label> 
						      
						      <div class="form-group">
						          <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Country code server variable'); ?></label> 
						          <input class="form-control" type="text" name="ServerVariableGEOIP_COUNTRY_CODE" value="<?php isset($geo_data['mod_geo_ip_country_code']) ? print $geo_data['mod_geo_ip_country_code'] : print 'GEOIP_COUNTRY_CODE' ?>"> 
						      </div>
						      
						      <div class="form-group">
						          <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Country name server variable'); ?></label> 
						          <input class="form-control" type="text" name="ServerVariableGEOIP_COUNTRY_NAME" value="<?php isset($geo_data['mod_geo_ip_country_name']) ? print $geo_data['mod_geo_ip_country_name'] : print 'GEOIP_COUNTRY_NAME' ?>"> 
						      </div>
						      
						      <div class="form-group">
						          <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','City name server variable'); ?></label> 
						          <input class="form-control" type="text" name="ServerVariableGEOIP_CITY" value="<?php isset($geo_data['mod_geo_ip_city_name']) ? print $geo_data['mod_geo_ip_city_name'] : print 'GEOIP_CITY' ?>"> 
						      </div>
						      
						      <div class="form-group">
						          <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Region name server variable'); ?></label> 
						          <input class="form-control" type="text" name="ServerVariableGEOIP_REGION" value="<?php isset($geo_data['mod_geo_ip_region_name']) ? print $geo_data['mod_geo_ip_region_name'] : print 'GEOIP_REGION' ?>"> 
						      </div>
						      
						      <div class="form-group">			      
						          <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Latitude variable'); ?></label>
							      <input class="form-control" type="text" name="ServerVariableGEOIP_LATITUDE" value="<?php isset($geo_data['mod_geo_ip_latitude']) ? print $geo_data['mod_geo_ip_latitude'] : print 'GEOIP_LATITUDE' ?>"> 
							  </div>
							  		
							  <div class="form-group">							  
							     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Longitude variable'); ?></label> 
							     <input class="form-control" type="text" name="ServerVariableGEOIP_LONGITUDE" value="<?php isset($geo_data['mod_geo_ip_longitude']) ? print $geo_data['mod_geo_ip_longitude'] : print 'GEOIP_LONGITUDE' ?>"> 
							  </div>
							  
							</div>						
						</div>
						
						
						<div role="tabpanel" class="tab-pane <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'max_mind') ? print 'active' : ''?>" id="maxmind">
						    <label><input type="radio" name="UseGeoIP" value="max_mind" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'max_mind') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Use MaxMind, does not depend on any third party remote service'); ?></label>

								<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','You can download city/country database from.'); ?>&nbsp;<a target="_blank" href="http://dev.maxmind.com/geoip/geoip2/geolite2/">MaxMind</a>
								</p>

								<p>
      	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','bcmath php extension detected'); ?> - <?php echo extension_loaded ('bcmath' ) ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>'; ?>
      	</p>

      	                        <div class="form-group">
								    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Location of city database'); ?></label> 
								    <input class="form-control" type="text" name="CityGeoLocation" value="<?php isset($geo_data['max_mind_city_location']) && ($geo_data['max_mind_city_location'] != '') ?  print htmlspecialchars($geo_data['max_mind_city_location']) : print 'var/external/geoip/GeoLite2-City.mmdb' ?>" />
                                </div>
								
								<div class="row">
									<div class="col-xs-6">
										<label><input type="radio" name="MaxMindDetectionType" value="country" <?php (isset($geo_data['max_mind_detection_type']) && $geo_data['max_mind_detection_type'] == 'country') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User country based detection, faster')?></label>
									</div>
									<div class="col-xs-6">
				<?php if (file_exists("var/external/geoip/GeoLite2-Country.mmdb")) : ?> <span class="label label-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','File exists'); ?>">var/external/geoip/GeoLite2-Country.mmdb</span> <?php else : ?><span class="label label-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','File does not exists'); ?>">var/external/geoip/GeoLite2-Country.mmdb</span><?php endif;?>
			</div>
								</div>
								<div class="row">
									<div class="col-xs-6">
										<label><input type="radio" name="MaxMindDetectionType" value="city" <?php (isset($geo_data['max_mind_detection_type']) && $geo_data['max_mind_detection_type'] == 'city') ? print 'checked="checked"' : '' ?> <?php if (!file_exists(isset($geo_data['max_mind_city_location']) && ($geo_data['max_mind_city_location'] != '') ?  $geo_data['max_mind_city_location'] : 'var/external/geoip/GeoLite2-City.mmdb')) : ?> disabled <?php endif;?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User city based detection, slower')?></label>
									</div>
									<div class="col-xs-6">
				<?php if (file_exists(isset($geo_data['max_mind_city_location']) && ($geo_data['max_mind_city_location'] != '') ?  $geo_data['max_mind_city_location'] : 'var/external/geoip/GeoLite2-City.mmdb')) : ?> <span class="label label-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','File exists');?>"><?php isset($geo_data['max_mind_city_location']) && ($geo_data['max_mind_city_location'] != '') ?  print htmlspecialchars($geo_data['max_mind_city_location']) : print 'var/external/geoip/GeoLite2-City.mmdb' ?></span> <?php else : ?><span class="round label alert" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','File does not exists')?>"><?php isset($geo_data['max_mind_city_location']) && ($geo_data['max_mind_city_location'] != '') ?  print htmlspecialchars($geo_data['max_mind_city_location']) : print 'var/external/geoip/GeoLite2-City.mmdb' ?></span><?php endif;?>
			</div>
								</div>
								
								<p>
									This product includes GeoLite2 data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
								</p>
												
						</div>
												
						<div role="tabpanel" class="tab-pane <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'php_geoip') ? print 'active' : ''?>" id="phpgeoip">
						      <div class="form-group">
						          <label><input type="radio" name="UseGeoIP" value="php_geoip" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'php_geoip') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Use PHP-GeoIP module'); ?></label>
								  <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Support for PHP-GeoIP detected'); ?> - <?php echo function_exists('geoip_country_code_by_name') ? '<span class="success label round">Yes</span>' : '<span class="round label alert">No</span>'; ?></p>
						      </div>
						</div>
												
						<div role="tabpanel" class="tab-pane <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'ipinfodbcom') ? print 'active' : ''?>" id="panel3">
						      <div class="form-group">
						         <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Requests will be comming from');?> - <?php echo erLhcoreClassIPDetect::getServerAddress(); ?></p>

								    <label class="inline"><input type="radio" name="UseGeoIP" value="ipinfodbcom" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'ipinfodbcom') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Use this service'); ?></label> 
								    
								    <div class="form-group">
								        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','API Key'); ?></label> 
								        <input class="form-control" type="text" name="ipinfodbAPIKey" value="<?php isset($geo_data['ipinfodbcom_api_key']) ? print htmlspecialchars($geo_data['ipinfodbcom_api_key']) : print '' ?>">
								    </div> 
						      </div>
						</div>
											
						<div role="tabpanel" class="tab-pane <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'locatorhq') ? print 'active' : ''?>" id="panel4">
						      <div class="form-group">

								 <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Requests will be comming from');?> - <?php echo erLhcoreClassIPDetect::getServerAddress(); ?></p>

								<label class="inline"><input type="radio" name="UseGeoIP" value="locatorhq" <?php isset($geo_data['geo_detection_enabled']) && ($geo_data['geo_service_identifier'] == 'locatorhq') ? print 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Use this service'); ?></label> 
								
								<div class="form-group">
								    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','API Key'); ?></label> 
								    <input class="form-control" type="text" name="locatorhqAPIKey" value="<?php isset($geo_data['locatorhq_api_key']) ? print htmlspecialchars($geo_data['locatorhq_api_key']) : print '' ?>"> 
								</div>
								
								<div class="form-group">
								    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Username'); ?></label> 
								    <input class="form-control" type="text" name="locatorhqUsername" value="<?php isset($geo_data['locatorhqusername']) ? print htmlspecialchars($geo_data['locatorhqusername']) : print '' ?>"> 
								</div>
								
								<div class="form-group">
								    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','IP, if your site remote IP is different from detected one, please provide correct remote IP address'); ?></label> 
								    <input class="form-control" type="text" name="locatorhqIP" value="<?php isset($geo_data['locatorhqip']) ? print htmlspecialchars($geo_data['locatorhqip']) : print erLhcoreClassIPDetect::getServerAddress() ?>"> 
                                </div>
								
						      </div>
						</div>
												
					</div>
				</div>
				
				<input type="submit" class="btn btn-default" name="StoreGeoIPConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Save'); ?>" />

			</form>
		</div>
		
		<div role="tabpanel" class="tab-pane" id="mapoptions">
		    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Drag a marker where you want to have map centered by default. Zoom is also saved.')?></p>

		    <div class="form-group">
		      <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Google Maps API key, saved automatically. After pasting the key, refresh the page.'); ?></label> 
		      <input class="form-control" type="text" id="id_GMapsAPIKey" value="<?php isset($geo_location_data['gmaps_api_key']) ? print $geo_location_data['gmaps_api_key'] : print '' ?>"> 
		    </div>

      		<div id="map_canvas" style="height:600px;width:100%;"></div>			
		</div>
		
	</div>
</div>

<script>
var marker = null;
var map = null;

function loadMapLocationChoosing(){

	$('#map-activator').click(function(){
		setTimeout(function(){
			google.maps.event.trigger(map, 'resize');
			map.setCenter(marker.getPosition());
		},500);
	});

	var mapOptions = {
		    zoom: <?php echo $geo_location_data['zoom'] ?>,
		    mapTypeId: google.maps.MapTypeId.ROADMAP,
		    disableDefaultUI: true,
	        options: {
	            zoomControl: true,
	            scrollwheel: true,
	            streetViewControl: true
	        },
		    center: new google.maps.LatLng(<?php echo $geo_location_data['lat'] ?>,<?php echo $geo_location_data['lng']?>)
		  };

	map = new google.maps.Map(document.getElementById('map_canvas'),mapOptions);

	var marker = new google.maps.Marker(
	{
	    map:map,
	    draggable:true,
	    animation: google.maps.Animation.DROP,
	    position: new google.maps.LatLng(<?php echo $geo_location_data['lat'] ?>,<?php echo $geo_location_data['lng']?>)
	});

	google.maps.event.addListener(map, 'zoom_changed', function() {
		 var pos = marker.getPosition();
		 $.postJSON('<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>/',{gmaps_api_key:$('#id_GMapsAPIKey').val(),zoom:map.getZoom(),store_map:1,csfr_token:confLH.csrf_token,lat:pos.lat().toFixed(4),lng:pos.lng().toFixed(4)}, function(data){

	     });
	});

	google.maps.event.addListener(marker, 'dragend', function(evt) {
	    $.postJSON('<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>/',{gmaps_api_key:$('#id_GMapsAPIKey').val(),zoom:map.getZoom(),store_map:1,csfr_token:confLH.csrf_token,lat:evt.latLng.lat().toFixed(4),lng:evt.latLng.lng().toFixed(4)}, function(data){

    	});
	});

	$('#id_GMapsAPIKey').keyup(function() {

		var pos = marker.getPosition();
		
		if (marker != null && map != null && typeof pos != 'undefined') {    		
    		$.postJSON('<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>/',{gmaps_api_key:$('#id_GMapsAPIKey').val(),zoom:map.getZoom(),store_map:1,csfr_token:confLH.csrf_token,lat:pos.lat().toFixed(4),lng:pos.lng().toFixed(4)}, function(data){
    		});
		} else {
			var pos = marker.getPosition();
    		$.postJSON('<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>/',{gmaps_api_key:$('#id_GMapsAPIKey').val(),store_map:1,csfr_token:confLH.csrf_token}, function(data){
    		});
		}
	});
};

$('#id_GeoDetectionEnabled').change(function(){
    if ($(this).is(':checked')){
        $('#settings-geo').removeClass('hide');
    } else {
        $('#settings-geo').addClass('hide');
    };   
});
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false)) {echo 'key=' , erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false) , '&';} elseif (isset($geo_location_data['gmaps_api_key'])) {echo 'key=' ,$geo_location_data['gmaps_api_key'], '&';}?>callback=loadMapLocationChoosing"></script>