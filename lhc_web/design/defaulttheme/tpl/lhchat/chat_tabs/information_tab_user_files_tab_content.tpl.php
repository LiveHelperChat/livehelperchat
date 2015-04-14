 <?php if ( isset($fileData['active_admin_upload']) && $fileData['active_admin_upload'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator') ) : ?>
  <div role="tabpanel" class="tab-pane<?php if ($chatTabsOrderDefault == 'information_tab_user_files_tab') print ' active';?>" id="main-user-info-files-<?php echo $chat->id?>">		   
      <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files.tpl.php'));?>		    
  </div>
<?php endif; ?>	