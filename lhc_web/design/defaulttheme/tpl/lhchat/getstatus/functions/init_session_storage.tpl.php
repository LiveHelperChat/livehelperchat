initSessionStorage : function(){
	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>
	if (localStorage && localStorage.getItem('lhc_ses')) {
		this.cookieData = this.JSON.parse(localStorage.getItem('lhc_ses'));
	} else {
	<?php endif;?>
    	var cookieData = lhc_Cookies('lhc_ses');
		if ( typeof cookieData === "string" && cookieData ) {
			this.cookieData = this.JSON.parse(cookieData);
		}
	<?php if ($trackDomain == '' && $disableHTML5Storage == 0) : ?>}<?php endif;?>
},