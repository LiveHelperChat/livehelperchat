<?php if ($currentUser->isLogged() == true) : ?>

<?php if ($currentUser->hasAccessTo('lhchat','chattabschrome')) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/chattabs.tpl.php'));?>
	
	<script>
	if (!!window.postMessage) {
		var currentPendingInitial = $('.pn-cnt').text();
		setInterval(function(){
			var currentPending = $('.pn-cnt').text();
			if (currentPendingInitial != currentPending){
				currentPendingInitial = currentPending;
				try {
					parent.postMessage('lhc_chrome:'+currentPendingInitial, '*');
				} catch(e) {
	
				};
			};
		},7000);
	};
	</script>
	
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

