<div id="chrome">
	<div class="row">
		<div class="col-xs-6 columns">		
			<div class="row">       
	          <div class="col-xs-9 columns">
	           <input id="awesomebar" name="url" value="<?php echo htmlspecialchars($browse->url)?>" type="text">
	          </div>
	          <div class="col-xs-3 columns">
	            	<a href="#" class="button prefix" onclick="return lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_cobrowse:<?php echo $browse->chat_id?>_<?php echo $browse->chat->hash?>')"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Request screen share')?>" class="icon-eye"></i></a>
	          </div>
	        </div>
		</div>
		<div class="col-xs-6 columns">
			<label class="inline left" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Show my mouse position to visitor')?>"><input type="checkbox" value="on" id="show-operator-mouse" ><i class="icon-mouse"></i></label> <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','On highlight scroll user window location to match my')?>" class="inline left"><input id="scroll-user-window" value="on" type="checkbox"><i class="icon-window"></i></label> <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Follow user scroll position')?>" class="inline left"><input id="sync-user-scroll" value="on" type="checkbox"><i class="icon-arrow-combo"></i></label> <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','On click navigate user browser')?>" class="inline left"><input id="status-icon-control" value="on" type="checkbox"><i class="icon-keyboard"></i></label>
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowredirect')) : ?>
			<a class="icon-network left" onclick="lhinst.redirectToURL('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Please enter a URL');?>')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect user to another url');?>"></a>
			<?php endif;?>
			
			<i title="" id="status-icon-sharing" class="left icon-eye<?php echo $browse->is_sharing == 0 ? ' eye-not-sharing' : ''?>"></i>
			<label id="last-message" class="inline right"><?php echo $browse->mtime > 0 && $browse->initialize != '' ? $browse->mtime_front : '' ?></label>
		</div>
	</div>	
</div>

<div id="contentWrap">
<div class="row h100proc">
    <div class="columns col-xs-3 pr-0 h100proc">
        <?php $chat_id = $chat->id;$chat_to_load = $chat;?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/single.tpl.php')); ?>
    </div>
    <div class="columns col-xs-9 h100proc">        
        	<div id="center-layout">
                <iframe id="content" name="content" src="<?php echo erLhcoreClassDesign::baseurl('cobrowse/mirror')?>" frameborder="0"></iframe>
            </div>       
    </div>
</div>
 </div>


<script>
<?php include(erLhcoreClassDesign::designtpl('lhcobrowse/operatorinit.tpl.php')); ?>
</script>