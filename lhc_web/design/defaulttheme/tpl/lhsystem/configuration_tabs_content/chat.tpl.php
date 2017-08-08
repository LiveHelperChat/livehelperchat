<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_content_chat_enabled == true && $currentUser->hasAccessTo('lhchat','use')) : ?>
<div role="tabpanel" class="tab-pane" id="chatconfiguration">
		<div class="row">
			<div class="col-md-6">
				<ul>
        		    <?php if ($currentUser->hasAccessTo('lhdepartment','list')) : ?>
        		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('department/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
        		    <?php endif; ?>
        		   
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/blockusers.tpl.php'));?>
        		    
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_configuration.tpl.php'));?>

                    <?php if ($currentUser->hasAccessTo('lhsystem','transferconfiguration')) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/transfer_configuration.tpl.php'));?>
                    <?php endif; ?>

        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/administrategeoconfig.tpl.php'));?>
        		    
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/geoadjustment.tpl.php'));?>
        			
        			<?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
        			<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/syncandsoundesetting.tpl.php'));?>
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/start_chat_form_settings.tpl.php'));?>
        		    
        		    <?php endif;?>
        		    		    
         			<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/translation.tpl.php'));?>
        		    
                    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/cannedmsg.tpl.php'));?>
                    
                    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/survey.tpl.php'));?>
                   
        		    <?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>
        
        		   		<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive.tpl.php'));?>	
        		   		
        		   		<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_variables.tpl.php'));?>	
        		   		
        		   		<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_events.tpl.php'));?>	
        
        			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/autoresponder.tpl.php'));?>
        	    
        		    <?php endif; ?>
        
                    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/xmpp.tpl.php'));?>
        	
        	        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_list.tpl.php'));?>
        
        		    <?php if ($currentUser->hasAccessTo('lhchatarchive','archive')) : ?>
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_archive.tpl.php'));?>
        		    <?php endif; ?>
        			
        			<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/statistic.tpl.php'));?>
        		    
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/maintenance.tpl.php'));?>
        		    
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/product.tpl.php'));?>
        		    
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/paidchat.tpl.php'));?>
        		    
        		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/restapi.tpl.php'));?>
        		    						    
        		</ul>
			</div>
			<div class="col-md-6">
    		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/files.tpl.php'));?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/theming.tpl.php'));?>
    		</div>
		</div>
	</div>
<?php endif;?>
    
    
