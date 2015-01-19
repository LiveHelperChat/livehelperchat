<input type="hidden" name="user_timezone" value="" />
<script>
$(document).ready(function() {
	
	Date.prototype.stdTimezoneOffset = function() {
	    var jan = new Date(this.getFullYear(), 0, 1);
	    var jul = new Date(this.getFullYear(), 6, 1);
	    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
	};

	Date.prototype.dst = function() {
	    return this.getTimezoneOffset() < this.stdTimezoneOffset();
	};
	
	var today = new Date();
	var timeZoneOffset = 0;
	
	if (today.dst()) { 
		timeZoneOffset = today.getTimezoneOffset();
	} else {
		timeZoneOffset = today.getTimezoneOffset()-60;
	};
	
  	$('input[name=user_timezone]').val(((timeZoneOffset)/60) * -1);
});
</script>
