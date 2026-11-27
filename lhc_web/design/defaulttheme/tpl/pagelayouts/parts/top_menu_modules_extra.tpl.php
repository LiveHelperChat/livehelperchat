<?php if ($hasExtensionModule) : ?>
     <li class="nav-item">
           <a href="#" class="nav-link"><i class="material-icons">info_outline</i><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/extra_modules_title.tpl.php'));?><i class="material-icons arrow md-18">chevron_right</i></a>
           <ul class="nav nav-second-level collapse">
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/extension_module_multiinclude.tpl.php'));?>
          </ul>
    </li>
<?php endif; ?>