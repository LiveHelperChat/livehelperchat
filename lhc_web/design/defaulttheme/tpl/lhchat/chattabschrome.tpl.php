<?php if ($currentUser->isLogged() == true) : ?>

<?php if ($currentUser->hasAccessTo('lhchat','chattabschrome')) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/chattabs.tpl.php'));?>
	
	<?php if ($is_popup === false) : ?>
	<script>
	if (!!window.postMessage) {
		var currentPendingInitial = $('.pn-cnt').text();
		setInterval(function(){
			var currentPending = $('.pn-cnt').text();
			if (currentPendingInitial != currentPending){
				currentPendingInitial = currentPending;
				try {
					parent.postMessage('lhc_chrome:'+currentPendingInitial.replace(/\(|\)/gi, ""), '*');					
				} catch(e) {};
			};
		},5000);
		parent.postMessage('lhc_chrome:'+currentPendingInitial.replace(/\(|\)/gi, ""), '*');
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

