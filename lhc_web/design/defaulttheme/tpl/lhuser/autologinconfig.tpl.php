<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

	<div role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#autologinsettings" aria-controls="autologinsettings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Auto login settings');?></a></li>	
			<li role="presentation"><a href="#autologincustom" aria-controls="autologincustom" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Custom auto logins');?></a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="autologinsettings">
			
			    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','This module can be used if you are generating autologin link. See site for code examples')?></p>
			
				<div class="form-group">
					<label><input type="checkbox" name="enabled" value="on" <?php (isset($autologin_data['enabled']) && $autologin_data['enabled'] == 1) ? print 'checked="checked"' : print '' ?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Enabled');?></label> 
				</div>
				
				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Secret hash used for authentification token generation, min 10 characters');?></label> 
					<input type="text" class="form-control" name="secret_hash" value="<?php (isset($autologin_data['secret_hash']) && $autologin_data['secret_hash'] != '') ? print htmlspecialchars($autologin_data['secret_hash']) : print '' ?>" />
				</div>
				
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
				 
				<div class="btn-group" role="group" aria-label="...">
					<input type="submit" class="btn btn-default" name="StoreAutologinSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
				</div>
			</div>
            <div role="tabpanel" class="tab-pane" id="autologincustom">
                <script>
                    function updateURL(index) {
                       $('#copy-url-'+index).val( $('#copy-url-'+index).attr('data-original-url') + $('#site-access-'+index).val() + '<?php echo erLhcoreClassDesign::baseurldirect('user/autologinuser')?>' + '/'+$('#secret-hash-'+index).val());
                    }
                    function copyURL(inst) {
                        $('#copy-url-'+inst.attr('data-index')).select();
                        document.execCommand("copy");
                        inst.tooltip({
                            trigger: 'click',
                            placement: 'top'
                        });
                        function setTooltip(message) {
                            inst.tooltip('hide')
                                .attr('data-original-title', message)
                                .tooltip('show');
                        }

                        function hideTooltip() {
                            setTimeout(function() {
                                inst.tooltip('hide');
                            }, 3000);
                        }
                        setTooltip(inst.attr('data-success'));
                        hideTooltip();
                        return false;
                    }
                </script>

                <?php for ($i = 0; $i < 5; $i++) : ?>
                <div class="row">
                    <div class="col-xs-1">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','User ID')?></label>
                            <input type="text" name="UserID[<?php echo $i?>]" class="form-control" value="<?php echo isset($autologin_data['autologin_options'][$i]) ? htmlspecialchars($autologin_data['autologin_options'][$i]['user_id']) : null?>" />
                        </div>
                    </div>
                    <div class="col-xs-1">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','SiteAccess')?></label>
                            <select id="site-access-<?php echo $i?>" name="siteAccess[<?php echo $i?>]" class="form-control" onchange="updateURL(<?php echo $i?>)">
                                <?php foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_site_access' ) as $locale ) : ?>
                                    <option value="<?php echo $locale?>" <?php (isset($autologin_data['autologin_options'][$i]['site_access']) && $autologin_data['autologin_options'][$i]['site_access'] == $locale) ? print 'selected="selected"' : ''?> ><?php echo $locale?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Default URL')?></label>
                            <input type="text" name="URL[<?php echo $i?>]" id="url-autologin-<?php echo $i?>" class="form-control" value="<?php echo isset($autologin_data['autologin_options'][$i]) ? htmlspecialchars($autologin_data['autologin_options'][$i]['url']) : null?>" />
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Secret hash')?></label>
                            <input type="text" onkeyup="updateURL(<?php echo $i?>)" name="SecretHash[<?php echo $i?>]" id="secret-hash-<?php echo $i?>" class="form-control" value="<?php echo isset($autologin_data['autologin_options'][$i]) ? htmlspecialchars($autologin_data['autologin_options'][$i]['secret_hash']) : null?>" />
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','IP Allowed')?></label>
                            <input type="text" class="form-control" placeholder="1.2.3.*,128.8.8.8" name="IP[<?php echo $i?>]" value="<?php echo isset($autologin_data['autologin_options'][$i]) ? htmlspecialchars($autologin_data['autologin_options'][$i]['ip']) : null?>" />
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly="readonly" id="copy-url-<?php echo $i?>" data-original-url="<?php echo erLhcoreClassSystem::$httpsMode == 'true' ? 'https://' : 'http://'?><?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('/')?>" value="">
                                <div class="input-group-addon"><a onclick="copyURL($(this))" data-index="<?php echo $i?>" data-original-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Copied!')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Copy URL to clipboard')?>"><i class="material-icons mr-0">&#xE14D;</i></a></div>
                            </div>
                        </div>
                    </div>


                </div>
                <script>updateURL(<?php echo $i?>)</script>
                <?php endfor; ?>

                <div class="btn-group" role="group" aria-label="...">
                    <input type="submit" class="btn btn-default" name="StoreAutologinSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
                </div>

            </div>
		</div>
	</div>
</form>