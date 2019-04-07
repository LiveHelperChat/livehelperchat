<?php if ($useFm || $useBo || $useChatbox || $useFaq || $useQuestionary || $hasExtensionModule) : ?>
     <li class="nav-item">
         <a href="#" class="nav-link"><i class="material-icons">&#xf2fd;</i><span class="nav-link-text"><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/extra_modules_title.tpl.php'));?></span><i class="material-icons arrow md-18">&#xf142</i></a>
           <ul class="nav nav-second-level">
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/questionary.tpl.php'));?>
  			  
			    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/faq.tpl.php'));?>
			  
			    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/chatbox.tpl.php'));?>
			  	
			    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/browseoffer.tpl.php'));?>
              
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/form.tpl.php'));?>
              
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/extension_module_multiinclude.tpl.php'));?>
          </ul>
    </li>
<?php endif; ?>