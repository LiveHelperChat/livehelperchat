<?php ob_start(); ?> <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/extension_module_multiinclude.tpl.php')); ?>
<?php $extensionModuleMenu = trim(ob_get_clean());?>

<?php if ($hasExtensionModule || $extensionModuleMenu !== '') : ?>
     <li class="nav-item">
           <a href="#" class="nav-link"><i class="material-icons">info_outline</i><?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/extra_modules_title.tpl.php'));?><i class="material-icons arrow md-18">chevron_right</i></a>
           <ul class="nav nav-second-level collapse">
                <?php echo $extensionModuleMenu;?>
          </ul>
    </li>
<?php endif; ?>