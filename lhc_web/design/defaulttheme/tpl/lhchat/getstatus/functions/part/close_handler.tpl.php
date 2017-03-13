var closeHandler = document.getElementById('lhc_close');
if (closeHandler !== null){
  closeHandler.onclick = function() { lhc_obj.hide(); lh_inst.chatClosedCallback('user'); return false; };
};