<?php 
/**
 * Just resize popup if dynamic height is required
 * */
if (isset($Result['dynamic_height'])) : ?>
<script>
$( window ).load(function() {
	if (window.opener && $('#user-popup-window').size() > 0) {
		var windowHeight = $('#user-popup-window').height()+90<?php if (isset($Result['dynamic_height_adjust'])) {echo $Result['dynamic_height_adjust'];}?>;		  
		window.resizeBy(0, windowHeight - $( window ).height());
	}
});
</script>
<?php endif;?>