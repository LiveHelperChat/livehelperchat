<?php if ($currentUser->isLogged() == true) : ?>

<?php if ($currentUser->hasAccessTo('lhchat','chattabschrome')) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/chattabs.tpl.php'));?>
	
	<input type="hidden" id="pending-counter-chrome" value="{{pending_chats.list.length != false && pending_chats.list.length > 0 ? pending_chats.list.length : 0}}" />
	<input type="hidden" id="unread-counter-chrome" value="{{unread_chats.list.length != false && unread_chats.list.length > 0 ? unread_chats.list.length : 0}}" />
	
	<?php if ($is_popup === false) : ?>
	<script>
	if (!!window.postMessage) {
		var currentPendingInitial = $('#pending-counter-chrome').val();
		var currentUnreadInitial = $('#unread-counter-chrome').val();
		setInterval(function(){
			var currentPending = $('#pending-counter-chrome').val();
			var currentUnreadPending = $('#unread-counter-chrome').val();
			
			if (currentPendingInitial != currentPending || currentUnreadPending != currentUnreadInitial) {
				currentPendingInitial = currentPending;
				currentUnreadInitial = currentUnreadPending;
				
				var notificationNumber = 0;
				
				if (parseInt(currentPendingInitial) >= 0){
					notificationNumber += parseInt(currentPendingInitial);
				};
				
				if (parseInt(currentUnreadPending) >= 0){
					notificationNumber += parseInt(currentUnreadPending);
				};
				
				if (parseInt(notificationNumber) >= 0) {
					try {
						parent.postMessage('lhc_chrome:'+(parseInt(notificationNumber) == 0 ? '' : parseInt(notificationNumber)), '*');					
					} catch(e) {};
				};				
			};
		},5000);		
	};
	</script>
	<?php endif;?>
	
<?php else : ?>
	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','You do not have permission to use chattabschrome function');?>
<?php endif;?>


<?php else : ?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Please');?> <a href="<?php echo erLhcoreClassDesign::baseurl('user/login')?>/(r)/<?php echo rawurlencode(base64_encode('chat/chattabschrome'))?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','login first.');?></a></h1>

<script>
setTimeout(function(){
	document.location = "<?php echo erLhcoreClassDesign::baseurl('chat/chattabschrome')?>";
},10000);	
</script>
<?php endif;?>

