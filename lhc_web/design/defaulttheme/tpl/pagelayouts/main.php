<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>

</head>
<body>

<div id="container">

<div id="main-header-bg"><div id="topcontainer">
<div id="logo"><h1><a href="<?php echo erLhcoreClassDesign::baseurl('/')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Home')?>"><img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' )?>" title="<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' )?>"></a></h1>
</div></div>

	
	<div class="clearer"></div>
</div>



<?php if (isset($Result['path'])) : 		
		$pathElementCount = count($Result['path'])-1;
		?>			
    		<div id="path">
    		  <?php foreach ($Result['path'] as $key => $pathItem) : ?>
    		      <?php 
    		      $pathElementRaquo = ($key != $pathElementCount) ? '&raquo;' : '';
    		      if (isset($pathItem['url'])) { ?>
    		             <a href="<?php echo $pathItem['url']?>"><?php echo $pathItem['title']?> <?php echo $pathElementRaquo;?> </a>		      
    		      <?php } else { ?>
    		      		 <?php echo $pathItem['title']?> <?php echo $pathElementRaquo;?>    
    		      <?php }; ?>
    		  <?php endforeach; ?>
    		</div>
		<?php endif; ?>




	<div id="bodcont" class="float-break">	
	
		<div id="leftmenucont">
		      <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/leftmenu.tpl.php'));?>		      
		</div>

		
				
		<div id="middcont">
			<div id="mainartcont">
			 <div style="padding:2px">
			<?php
			 echo $Result['content'];		
			?>			
			</div>
			</div>
		</div>
		
		<div id="rightmenucont">
		
			<div id="rightpadding">
									
					<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
					
					<div class="right-infobox">
                		<fieldset><legend><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats');?></a></legend>
                    		<div id="right-pending-chats">
                        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
                            </div>
                		</fieldset>
            		</div> 	
            				
					<div class="right-infobox">
                		<fieldset><legend><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats');?></a></legend>
                    		<div id="right-active-chats">
                        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
                            </div>
                		</fieldset>
            		</div>
            				
					<div class="right-infobox">
                		<fieldset><legend><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transfered chats');?></a></legend>
                    		<div id="right-transfer-chats">
                        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
                            </div>
                		</fieldset>
            		</div>           		
            				
										
		    </div>	
		</div>
		
	</div>
	
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>
<br />

</div>
<script type="text/javascript">
chatsyncadmininterface();
</script>
</body>

</html>