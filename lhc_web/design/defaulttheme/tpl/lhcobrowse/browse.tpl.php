<div id="chrome">
	<div class="row">
		<div class="large-6 columns">		
			<div class="row collapse">       
	          <div class="small-9 columns">
	           <input id="awesomebar" name="url" value="<?php echo htmlspecialchars($browse->url)?>" type="text">
	          </div>
	          <div class="small-3 columns">
	            	<a href="#" class="button prefix" onclick="return lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_cobrowse:<?php echo $browse->chat_id?>_<?php echo $browse->chat->hash?>')"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Request screen share')?>" class="icon-eye"></i></a>
	          </div>
	        </div>
		</div>
		<div class="large-6 columns">
			<label class="inline left" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Show my mouse position to visitor')?>"><input type="checkbox" value="on" id="show-operator-mouse" ><i class="icon-mouse"></i></label><label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','On highlight scroll user window location to match my')?>" class="inline left"><input id="scroll-user-window" value="on" type="checkbox"><i class="icon-window"></i></label>
			<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','allowredirect')) : ?>
			<a class="icon-network left" onclick="lhinst.redirectToURL('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Please enter a URL');?>')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect user to another url');?>"></a>
			<?php endif;?>
			<i title="" id="status-icon-sharing" class="left icon-eye<?php echo $browse->is_sharing == 0 ? ' eye-not-sharing' : ''?>"></i>
			<label id="last-message" class="inline right"><?php echo $browse->mtime > 0 && $browse->initialize != '' ? $browse->mtime_front : '' ?></label>
		</div>
	</div>	
</div>

<div id="contentWrap">
	<div id="center-layout">
        <iframe id="content" name="content" src="<?php echo erLhcoreClassDesign::baseurl('cobrowse/mirror')?>" frameborder="0"></iframe>
    </div>
</div>

<script>
<?php include(erLhcoreClassDesign::designtpl('lhcobrowse/operatorinit.tpl.php')); ?>
</script>