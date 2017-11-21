var closeHandler = document.getElementById('<?php echo $chatCSSPrefix?>_close');
if (closeHandler !== null){
  closeHandler.onclick = function() { lhc_obj.hide(); lh_inst.chatClosedCallback('user'); return false; };
};