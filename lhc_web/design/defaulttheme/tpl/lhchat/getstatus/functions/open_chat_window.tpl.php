lh_openchatWindow : function() {    	
    <?php if ($click == 'internal') : ?>
    this.showStartWindow();
    <?php else : ?>
    this.lhc_need_help_hide();
    var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
     
    var popupHeight = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.popup_height != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.popup_height) : 520;
    var popupWidth = (typeof <?php echo $chatOptionsVariable?> != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt != 'undefined' && typeof <?php echo $chatOptionsVariable?>.opt.popup_width != 'undefined') ? parseInt(<?php echo $chatOptionsVariable?>.opt.popup_width) : 500;
    window.open(this.urlopen()+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments(),this.windowname,"scrollbars=yes,menubar=1,resizable=1,width="+popupWidth+",height="+popupHeight);
    this.chatOpenedCallback(this.isOnline == false ? 'external_offline' : 'external');
    <?php endif; ?>
    return false;
},