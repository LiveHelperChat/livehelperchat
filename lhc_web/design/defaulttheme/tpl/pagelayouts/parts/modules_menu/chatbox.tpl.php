<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/chatbox_pre.tpl.php'));?>
<?php if ($pagelayouts_parts_modules_menu_chatbox_enabled == true && $useChatbox) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/configuration')?>"><i class="material-icons">comment</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chatbox');?></a></li>
<?php endif; ?>	