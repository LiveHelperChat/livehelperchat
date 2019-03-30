<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/chatbox_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_chatbox_enabled == true && $useChatbox) : ?>
<li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('chatbox/configuration')?>"><i class="material-icons">comment</i><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chatbox');?></span></a></li>
<?php endif; ?>	