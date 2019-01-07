<?php 
/**
 * Just resize popup if dynamic height is required
 * */
if (isset($Result['dynamic_height'])) : ?>
<script>
$( window ).on('load',function() {
	if (window.opener && $('#user-popup-window').length > 0) {
		var windowHeight = $('#user-popup-window').height()+60<?php if (isset($Result['dynamic_height_adjust'])) {echo $Result['dynamic_height_adjust'];}?>;
		// Don't do anything like popup is bigger than our screen
		if (screen.availHeight > (windowHeight+60)){
			  window.resizeBy(0, windowHeight - $( window ).height());
		}
	}
});
</script>
<?php endif;?>