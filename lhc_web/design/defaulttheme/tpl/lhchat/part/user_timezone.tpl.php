<input type="hidden" name="user_timezone" value="" />
<script>
$(document).ready(function() {
  	$('input[name=user_timezone]').val(((new Date().getTimezoneOffset())/60) * -1);
});
</script>
