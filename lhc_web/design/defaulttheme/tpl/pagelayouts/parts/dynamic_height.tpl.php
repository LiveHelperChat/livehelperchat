<?php 
/**
 * Just resize popup if dynamic height is required
 * */
if (isset($Result['dynamic_height'])) : ?>
<script>
$( window ).load(function() {
	if (window.opener && $('#user-popup-window').size() > 0) {
		var windowHeight = $('#user-popup-window').height()+90<?php if (isset($Result['dynamic_height_adjust'])) {echo $Result['dynamic_height_adjust'];}?>;	

		// Don't do anything like popup is bigger than our screen		
		if (screen.availHeight > (windowHeight+90)){		
			  window.resizeBy(0, windowHeight - $( window ).height());
		}
	}
});
</script>
<?php endif;?>