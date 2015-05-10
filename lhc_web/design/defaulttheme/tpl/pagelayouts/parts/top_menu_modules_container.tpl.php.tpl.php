<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_extension_multiinclude.tpl.php.tpl.php'));?>	

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/modules_permissions.tpl.php'));?>	

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_extension_module_multiinclude.tpl.php'));?>
		
<?php if ($useFm || $useBo || $useChatbox || $useFaq || $useQuestionary || $hasExtensionModule) : ?>
		<li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/extra_modules_title.tpl.php'));?> <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
             
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/questionary.tpl.php'));?>
	  			  
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/faq.tpl.php'));?>
			  
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/chatbox.tpl.php'));?>
			  	
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/browseoffer.tpl.php'));?>
              
              <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/form.tpl.php'));?>
              
              <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/extension_module_multiinclude.tpl.php'));?>
			            
            </ul>
        </li>		
<?php endif; ?> 